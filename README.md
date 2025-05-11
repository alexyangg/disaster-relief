# Disaster Relief App

## Using public_html 
On public_html, if any files are unable to load due to permission errors, run command
`chmod -R 755 project_e0g8g_g1q1p_q4p5y/` (Would need to be run every time a php file is added/moved)

## Connecting to Oracle DB
Add a `config.ini` file to project's root directory with the following, changing "cwl" to your own CWL and `dbpassword` to 'a' + your student number.
```c
[database]
dbuser = "ora_cwl"
dbpassword = "a12345678"
```
