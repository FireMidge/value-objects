# Development

## Table of Contents

- [Tests](#tests)
  - [Set-up](#set-up)
  - [Run tests](#run-tests)
- [Dependencies](#dependencies)
- [Commits](#commits)
  - [Validate composer.json](#validate-composerjson)
  - [Update CHANGELOG.md](#update-changelogmd)
  - [Regenerate badges with new code coverage scores](#regenerate-badges-with-new-code-coverage-scores)
  - [Regenerate the README.md file](#regenerate-the-readmemd-file)
  - [Tag your commit](#tag-your-commit)


## Tests


### Set-up
In order to be able to run tests, go through the following steps to set up the Docker environment.


#### Download and build the Docker image
`docker-compose build --build-arg UID=`id -u` lib{v}`

*Replace `{v}` with the relevant PHP version number.*


#### Install dependencies

`docker-compose run lib{v} composer install`

*Replace `{v}` with the relevant PHP version number.*


### Run tests

#### Multiple PHP versions

Since version `2.7`, we have more than one Dockerfile, to run the tests against more than one PHP version.

We haven't separated the `vendor` folder yet (which we could probably do by creating separate `vendor` directories for each container and then mounting them as `/src/vendor` volumes in the `docker-compose.yml`), as we're not increasing the minimum required PHP version dependency.

In order to run the PHP 8.1 container, you'd use:
`docker compose run --rm lib81 vendor/bin/phpunit`

In order to run the PHP 8.4 container, you'd use:
`docker compose run --rm lib84 vendor/bin/phpunit`

A standalone "lib" service is no longer available, so wherever you see `run --rm lib84` in this file, update the last 2 numbers to run the relevant PHP version (if available), e.g. `run --rm lib81`. (The `--rm` flag makes sure that the container instance is removed once the command has finished executing, avoiding orphans).


#### Commands to run tests

To run mutation tests:
`docker compose run --rm lib84 php infection.phar`

To run the unit tests:
`docker compose run --rm lib84 vendor/bin/phpunit`

To create a code coverage report in HTML style:
`docker compose run --rm lib84 vendor/bin/phpunit --coverage-html ./coverage-report`

To create a quick code coverage overview in the CLI:
`docker compose run --rm lib84 vendor/bin/phpunit --coverage-text`

*Replace `{v}` with the relevant PHP version number.*

#### Run a specific test

To run a specific class, or method, run:

`docker compose run --rm lib84 vendor/bin/phpunit --filter testWithValueDoesNotChangePreExisting`

where `testWithValueDoesNotChangePreExisting` is the name of the method. You can also use the name of a class instead.

and `{v}` is the PHP version number.

To run a specific data set (when using data providers), you can use the name of the data set after the `@`, e.g.:

` docker compose run --rm lib84 vendor/bin/phpunit --filter testWithValueDoesNotChangePreExisting@invalidNumber`

where `testWithValueDoesNotChangePreExisting` is the name of the method, and `invalidNumber` is the name of the data set. Note that this only works with non-numeric data set names.


#### Adding new tests

Remember that each test method name needs to start with "test", otherwise it will be ignored by PhpUnit.


#### Removing orphaned containers

If you see a lot of container names when running tests and the error message `If you removed or renamed this service in your compose file, you can run this command with the --remove-orphans flag to clean it up.`, you'll probably trust the message and run the same command again but with the `--remove-orphans` flag.
This then results in you seeing `Unknown option "--remove-orphans"`, and thinking "...Wtf?"

Despite the message telling you otherwise, the `--remove-orphans` flag is only available on the `docker compose up` and `docker compose down` command, so just run the following to clear them:

```bash
docker compose up --remove-orphans
```

In order to avoid orphans in the first place, run all commands with `--rm`, which removes the container automatically once it's finished executing. 


## Dependencies

### Adding new dev dependencies

Use the --dev option when requiring new dev dependencies via composer:
`docker compose run --rm lib84 composer require --dev phpunit/phpunit ^9`


## Commits

- [Validate `composer.json`](#validate-composerjson)
- [Update `CHANGELOG.md`](#update-changelogmd) and briefly describe any additions/changes/backwards-compatibility-breaking changes
- Update `docs/README.md` with any new features. Change any text that is no longer accurate
- [Run PhpUnit and Infection](#run-tests) - inspect the Infection log and fix any regressions. Make sure new code coverage isn't lower than previously.
- [Regenerate badges](#regenerate-badges-with-new-code-coverage-scores) with new code coverage scores, and update the Alt text in the Quality Control section in `docs/README.md` for blind developers
- [Regenerate the main `README.md` file](#regenerate-the-readmemd-file)
- Create the PR
- Use "squash & merge" as the merge option
- After the branch has been merged into master, pull the latest `master` and tag the latest commit with the new version
- Create new release in Github if new features have been added or the latest version contains backwards-compatibility-breaking changes. Can re-use the same text as in `CHANGELOG.md` for the release.

Any slightly more "complex" tasks have been detailed below.

### Validate composer.json

Run `docker compose run --rm lib84 composer validate` to make sure `composer.json` is still valid.


### Update CHANGELOG.md

Add a line in CHANGELOG.md. Make sure you place the newest version on top of the log.

If there are versions missing in CHANGELOG, add them. These commands should help:

`git tag -n`
: Shows a list of existing (annotated) tags with their descriptions.

`git rev-list -n 1 v1.0`
: Returns the hash of the commit tagged with "v1.0".

`git show 338a601819077e11fdc439e73079d49dd08319ca`
: Using the commit hash from the previous command, you can see what's changed in it as well as what its commit message was.

### Regenerate badges with new code coverage scores

Run this command (but don't forget substituting values with the current values):
`docker compose run --rm lib84 php docs/generateBadges.php --cc=0 --msi=0 --mcc=0 --ccm=0`

*Round all percentages to 0 decimals. Round down until .49, round up from .5.*

#### cc
`cc` is the Code Coverage percentage of covered methods. You get this value by running `docker compose run --rm lib84vendor/bin/phpunit --coverage-text` and taking the "Methods" percentage from the summary section.

#### msi, mcc, ccm
All of these values are taken from the Infection summary. Run `docker compose run --rm lib84php infection.phar`, which gives you a "Metrics" section, from which you take the following percentages:

msi: Mutation Score Indicator

mcc: Mutation Code Coverage

ccm: Code Coverage MSI

**Don't forget to also update the Alt text in `README.md` in the `Quality Control` table for each of the badges.** Using images was the only way to be able to display colour-coded stats but for them to remain accessible to blind developers, it's important to keep the "alt" text up-to-date.


### Regenerate the README.md file

Run this from the root project folder:
`node_modules/.bin/markdown-include ./markdown.json`

Make sure that you have run `npm i --production=false` first.

The syntax to include other README files is as follows:
```markdown
#include "docs/YOUR_INCLUDED_FILE.md"
```

Any include paths inside `docs/README.md` must be relative to the **root** folder (i.e. prefixed with "docs").


### Tag your commit

#### View existing tags
You can see the list of current tags with `git tag`.

You can also filter out tags, with `git tag -l "v1.8.5*"`

To see the description of a specific tag (along with the description of the associated commit), use:
```bash
git show {TAG_NAME}
```
e.g.: `git show v2.1`


##### View existing tags with their associated commits

To see the list of tags with their associated commit and type, use:
```bash
git for-each-ref refs/tags 
```
The first column refers to the commit hash, the 2nd is the type. `tag` means it's an annotated tag, whereas `commit` means lightweight tag.


#### Types of tags
There are two types of tags: **lightweight** and **annotated**.
Lightweight tags are nothing but a pointer to a specific commit, whereas annotated tags are stored as full objects in the Git database and contain tagger name, tagger e-mail address, date, a message, and more.
It is recommended to use annotated tags wherever possible unless you are just after a temporary tag.


#### Create a new tag
To create a new annotated tag, first commit as usual and then run:
`git tag -a v1.9 -m "Version 1.9. Changes xyz."`


#### Tag existing commit
You can also add a tag to a previous commit like so: `git tag -a v1.9 690c3c0e24` where `690c3c0e24` is the (part of the) commit checksum.


#### Push the tag(s)!
When pushing, don't forget to also push the tag(s):
`git push origin HEAD --tags`


#### Re-assign an annotated tag to another commit

1. Delete the tag on the remote: `git push origin :refs/tags/v1.9`
2. Then re-create the tag with the -f flag: `git tag -a v1.9 -m "Version 1.9: xyz" -f`
3. Push the new commit with the updated tag to the remote: `git push origin HEAD --tags`


#### Delete erroneous tag

If you want to completely delete a tag (as opposed to re-assigning it to another commit), use `git tag --delete {tagName}`, e.g. `git tag --delete 2.4`, where `2.4` was the erroneous tag, as it is missing the "v" prefix.
