# Kaizer B&B Deployment Guide for InfinityFree

## 1) Package your site
The site files are ready to upload as a ZIP package:
- [kaizerbnb.zip](kaizerbnb.zip)

## 2) Create your InfinityFree account
1. Go to https://www.infinityfree.com
2. Sign up or log in
3. Create a new hosting account and choose a subdomain (for example: yoursite.infinityfreeapp.com)

## 3) Upload your files
1. Open the InfinityFree control panel
2. Go to File Manager
3. Open the htdocs folder
4. Upload the ZIP file
5. Extract the ZIP contents into htdocs

## 4) Important hosting limitation
This project uses PHP and MySQL. InfinityFree free hosting usually does not provide a usable MySQL setup for the free plan, so you may need one of these:
- a remote MySQL host, or
- an InfinityFree paid plan/addon that includes database support

## 5) Database setup
If your hosting plan supports MySQL:
1. Create a database in InfinityFree
2. Update [db.php](db.php) with the new database host/user/password/database name
3. Open your site’s install page to create the tables

## 6) Admin login
After the site is live, use:
- Email: admin@kaizerbnb.com
- Password: 123456

## 7) Notes
- Email confirmation may not work on InfinityFree unless you configure a mail provider
- The project includes a local database bootstrap, but a real hosted database is still required for full functionality
