#!/bin/bash

# webDevCommand: install
# webDevBranch: project
# webDevBranchDescription: Commands for Project
# webDevDescription: Install Project

# Install dependencies
composer install

# Create the .env file
echo "APP_ENV=dev" > .env.local

# Install node_modules
yarn install

# Build the project
yarn encore dev