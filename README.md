### Symfony test task

- Create two applications using Symfony3 framework:
 1. server with API entries
 2. client

- Server stores users and groups in MySQL database.
user table: id, name, email
group table: id, name

 - Client accessing the server through the API:
 1. should be able to add, edit, delete users and groups on the server (well, CRUD)
 2. should be able to get report with the list of users of each group. 

## For launch
- vagrant required;
```bash
vagrant plugin install vagrant-hostmanager
vagrant up
```
- from docker:

for dev
```bash
make dev
```

for prod
```bash
make prod
```

