ARG SWOOLE_DOCKER_VERSION

FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN set -eux \
    && apt-get update && apt-get -y install libyaml-dev \
    && pecl install yaml \
    && docker-php-ext-enable yaml
