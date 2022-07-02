# Development

## Tests

### Set-up
In order to be able to run tests, go through the following steps to set up the Docker environment.

#### Download and build the Docker image
`docker-compose build --build-arg UID=`id -u` lib`

#### Install dependencies

`docker-compose run lib composer install`

### Run tests

To run the unit tests:
`docker-compose run lib vendor/bin/phpunit`

To run mutation tests:
`docker-compose run lib php infection.phar`

#### Adding new tests

Remember that each test method name needs to start with "test", otherwise it will be ignored by PhpUnit.

## Dependencies

### Adding new dev dependencies

Use the --dev option when requiring new dev dependencies via composer:
`docker-compose run lib composer require --dev phpunit/phpunit ^9`

## Commits

### Tag your commit

#### View existing tags
You can see the list of current tags with `git tag`.

You can also filter out tags, with `git tag -l "v1.8.5*"`

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