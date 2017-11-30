# User-Search

## Introduction
  This project allows you to search users (and organisations) on GitHub and save history of what you searched for.

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
  1. **Save the generated token (you won't be able to see it again)**
  1. Setup your web server and MySQL database

## Instalation
  1. Download / clone this repository
  1. Extract it to where ever on server you want (as long as it will accessible from the internet and you will know URL to it)
  1. Open `install.php` in browser
  1. Follow the steps in installer
  
## Permissions
  Please check that files in folders (and folders themselves) have the right permissions set.
  - Folder `css` and everything in it - `0755`
  - Folder `fonts` and everything in it - `0755`
  - All `.php` files - `0640`

## External libraries
  - [Dibi](https://github.com/dg/dibi)
