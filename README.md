# websiteblog

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/1fc9bb1cc9aa45fdbf177dbce09d70f3)](https://app.codacy.com/gh/Jersey276/TristanLefevre_5_29042021?utm_source=github.com&utm_medium=referral&utm_content=Jersey276/TristanLefevre_5_29042021&utm_campaign=Badge_Grade_Settings)

This website on is 1.0 version is composed of a post system with comment and user management system.

## Installation

1.  get all file from this branch
2.  Collect all composer dependencies with `composer require` and press enter
3.  Config your website with .env files with all needed data for work (see .env.example for create your own .env file)

## Content

This project contains basic functionnality of a blog website such has
-  Authentification system
-  Post system
-  Comment for post
-  User Management system

 ![Arborescence du site](https://user-images.githubusercontent.com/83149814/120319381-3d7d8b00-c2e1-11eb-9193-bcc59f33d991.png)

## How it work

![website system](https://user-images.githubusercontent.com/83149814/120321424-93ebc900-c2e3-11eb-9623-ac0d9e9ea83b.png)

### Route
Link used for target controller function
### Role Checker
Check if user have the right role for access to this area
### Controller
link between route, view and manager
### Manager
Call checking, database request and 
### Check
Verify Conformity of post data and other verification
### Query
Pre-build sql query, use Fluid class
### database
Connect to database, send prepared query and collect awnser
### mail
Send prebuild mail
### View
Display user awnser of his request. Use Twig to generate view
### Twig Function
Function for twig template (view)
