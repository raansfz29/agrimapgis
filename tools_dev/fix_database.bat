@echo off
REM Database Fix Script for AgriMapGIS
REM This script will reset the database and recreate all tables

echo.
echo ========================================
echo AgriMapGIS Database Reset Script
echo ========================================
echo.

echo Step 1: Rolling back all migrations...
php spark migrate:rollback --all

echo.
echo Step 2: Running fresh migrations...
php spark migrate

echo.
echo Step 3: Running seeder...
php spark db:seed AgriSeeder

echo.
echo ========================================
echo Database Setup Complete!
echo ========================================
echo.

pause
