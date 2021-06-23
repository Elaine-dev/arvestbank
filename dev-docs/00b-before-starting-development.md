# Steps to take every time you start development
In order to keep your local development in sync with the entire dev team. \
Please follow the following steps before you start a new Jira ticket.


### Review your JIRA ticket
Look at the JIRA ticket assigned to you and review your tasks before you start. 

### Fetch all of latest code changes from Github from the upstream 
```
$ git fetch --all
$ git checkout develop
$ git pull upstream develop
```

### confirm you do not have any local changes you dont want to overwrite

### Rebase your current branch with develop 
```
$ git rebase upstream/develop
```

### Sync your origin
Sync any changes from the upstream to your origin fork 
```
git push origin
````

### Create a feature branch from develop 
```
$ git checkout develop 
$ git checkout -b [JIRA-Ticket-number]-short-description 
````
for example 
```
$ git checkout -b ab-34-basic-page-content-type
```

### Run blt setup (wait for site studio config to load, this can take a while)
```
$ blt sync
```

### If you want to log into the site 
```
$ cd docroot
$ drush uli
```

### Commit any new changes and push back to origin 
You will need every commit to have the jira ticket number and a sentence that starts with a capital letter
and ends with a period or your commit will fail. 

```
$ git add . 
$ git commit -m"[JIRA-Ticket-number]: Decription of commits."
$ git push origin [JIRA-Ticket-number]-short-description 
```
