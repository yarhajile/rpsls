#!/bin/bash
# These permissions need to be set to give Apache/Symfony write access to the
# relevant parts of the file system.

ROOT_DIR=/deploy/rpsls

chmod -R 777 $ROOT_DIR/app/cache
chmod -R 777 $ROOT_DIR/app/logs
#chmod 777 $ROOT_DIR/src/AppBundle/Temp
