FROM php:8.1-cli-alpine

RUN \
    # Needed to install pecl libraries
    apk add g++ make autoconf && \
    # PCOV provides code coverage reports and is required by Infection
    pecl install pcov && docker-php-ext-enable pcov

# The following are required by the newer version of Infection (PHP 8), as it makes use of
# the Normalizer class. Normalizer requires the intl PHP extension, which in turn has
# icu-dev as a dependency.
RUN \
    apk add icu-dev && \
	docker-php-ext-configure intl && docker-php-ext-install intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Ensure any files created by the running application are owned by the user who first built the image
ARG UID
RUN if [ -z "$UID" ]; then echo "Build argument 'UID' was not set" 1>&2 && exit 1; fi
RUN echo "app:x:$UID:$UID::/home/app:" >> /etc/passwd \
    && echo "app:!:$(($(date +%s) / 60 / 60 / 24)):0:99999:7:::" >> /etc/shadow \
    && echo "app:x:$UID:" >> /etc/group \
    && mkdir /home/app \
    && chown app: /home/app
USER app
