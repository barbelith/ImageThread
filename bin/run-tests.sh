#!/usr/bin/env bash
vendor/bin/paratest --configuration=phpunit.xml.dist --phpunit=vendor/bin/phpunit -p 16 --runner WrapperRunner