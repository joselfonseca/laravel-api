#!/bin/bash

set -x #echo on

docker-compose -f docker-compose.yml exec app /bin/bash