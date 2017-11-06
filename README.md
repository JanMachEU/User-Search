# User-Search

## Introduction
  

## Requirements
  - PHP (version is based on used version of dibi, at least 5.4.4 but 7.1+ is better)
  - MySQL

## Tested on
  - PHP 7.1
  - MySQL 5.6.17
  - Apache on Linux

## Before instalation
  1. Read how to get [GitHub API Token](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line/)
  1. Get [GitHub API Token](https://help.github.com/articles/creating-a-personal-access-token-for-the-command-line/) with scopes `public_repo`, `read:org`, `read:user`, `repo:status`
  1. Save the generated token (you won't be able to see it again)

## Instalation
  1. Download it
  1. Setup your web server as usual
  1. Extract it to where ever on server you want (as long as it will accessible from the internet and you will know URL to it)
  1. Open `ADDRESS_BASED_ON_WHERE_YOU_EXTRACTED_IT/install.php` in browser
  1. Follow the steps in installer
  
## External libraries
  - [Dibi](https://github.com/dg/dibi)
