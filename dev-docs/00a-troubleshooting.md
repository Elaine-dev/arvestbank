# Troubleshooting 

## Public key denied
This happens when the ssh key inside of github does not match your local ssh key. You need to correctly forward your ssh key inside of your DrupalVM. \
Outside of your vm run. \ 
```
ssh-add ~/.ssh/id_rsa  
```
Then run `ssh-add ~/.ssh/id_rsa` \
You should have an output similar to (in particular look for Forward agent) \
```
Host arvestbank
  HostName 127.0.0.1
  User vagrant
  Port 2200
  UserKnownHostsFile /dev/null
  StrictHostKeyChecking no
  PasswordAuthentication no
  IdentityFile /Users/[username]/.vagrant.d/insecure_private_key
  IdentitiesOnly yes
  LogLevel FATAL
  ForwardAgent yes
```
Also, check to make sure your ssh agent is running `ssh-agent` \
Inside of your VM check to see if your key is added `ssh-add -L` 

If that does not work, copy your local ssh keys from  `~/.ssh/id_rsa` and add them inside of your vm
```
cd ~/.ssh/id_rsa`
cat id_rsa
cat id_rsa.pub`
```
and add them inside your your vm \
```
nano ~/.ssh/id_rsa
nano ~/.ssh/id_rsa.pub
```
Make sure your ssh keys have the correct permissions \
`chmod 600 ~/.ssh/id_rsa` \
`chmod 644 ~/.ssh/id_rsa.pub`


## Git conflicts 

### merge conflicts 
https://stackoverflow.com/questions/1628088/reset-local-repository-branch-to-be-just-like-remote-repository-head
`git reset --hard upstream/[branch name]`


## composer conflicts 
Some times composer will trip up when trying to download dependencies. Instead of trying to fix individual issues, delete the composer managed directories and start over. 
```
rm -rf docroot/core
rm -rf vendor
composer install 
```

#### composer memory issues 
```
php -d memory_limit=-1 /usr/local/bin/composer install
```

#### composer timeout too many file streams
```
Ulimit 1000
``` 


## Drush commands 
https://drushcommands.com/drush-9x/


## Useful commands

```bash 
blt doctor 
```

```
composer require - adds a new dependency
composer install - installs all dependencies
composer update <package> --with-all-dependencies - updates a package
composer why <package> - explains where a dependency originates
composer why-not <package> - explains why a dependency cannot be installed
composer show <package> - checks if a package is installed
composer clear-cache - nukes local composer cache
```

## Git Commands 
Common git commands
```
git command    definition
git remote show origin     shows the origin of the git repo. 
git fetch     updates your remote-tracking branches
git pull    pulls remote changes
git add -p    add each change individually to a single commit
git rebase --interactive HEAD~2    merge your commits together
git clean -fd    remove untracked files and directories
git stash    save and remove local changes but do not commit them
git stash pop    return saved change
sgit fetch
Git add -p lets you add your commits in small batches. By committing one change at a time, you can exclude changes that do not pertain to your commit message. This is helpful when other developers are reviewing your commit messages. Important to note that git add -p will only add changes to files, not add new files. You will still have to use git add for new files. git add -p.
 When you use git add -p you will see the options
 Stage this hunk [y,n,q,a,d,/,e,?]?
 These options in more detail.
y -    stage this hunk
n -    do not stage this hunk
q -    quit; do not stage this hunk or any of the remaining ones
a -    stage this hunk and all later hunks in the file
d -    do not stage this hunk or any of the later hunks in the file
/ -    search for a hunk matching the given regex
j -    leave this hunk undecided, see next undecided hunk
J -    do not stage this hunk or any of the later hunks in the file
k -    leave this hunk undecided, see next undecided hunk
s -    leave this hunk undecided, see next hunk
e -    manually edit the current hunk
? -    print help
```
