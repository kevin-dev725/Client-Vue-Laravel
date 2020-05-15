#!/bin/bash
#
# Copyright 2014 Amazon.com, Inc. or its affiliates. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License").
# You may not use this file except in compliance with the License.
# A copy of the License is located at
#
#  http://aws.amazon.com/apache2.0
#
# or in the "license" file accompanying this file. This file is distributed
# on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
# express or implied. See the License for the specific language governing
# permissions and limitations under the License.

# Usage: get_stack_type <EC2 instance ID>
#
#   Get Stack Type of instance. 0 for production, 1 for UAT, 2 for development
get_stack_type() {
	if [[ "$DEPLOYMENT_GROUP_NAME" == "prod" ]]; then
		return 0
	elif [[ "$DEPLOYMENT_GROUP_NAME" == "uat" ]]; then
		return 1
	elif [[ "$DEPLOYMENT_GROUP_NAME" == "dev" ]]; then
		return 2
	fi
    echo "Stack tag not found, failing"
    exit 1
}

# Usage: msg <message>
#
#   Writes <message> to STDERR only if $DEBUG is true, otherwise has no effect.
msg() {
    local message=$1
    $DEBUG && echo $message 1>&2
}

# Usage: error_exit <message>
#
#   Writes <message> to STDERR as a "fatal" and immediately exits the currently running script.
error_exit() {
    local message=$1

    echo "[FATAL] $message" 1>&2
    exit 1
}