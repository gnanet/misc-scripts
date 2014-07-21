owncloud related scripts
============
##IMPORTANT: USE AT OWN RISK!!##

**The scripts presented here are generalized versions of the scripts i used to solve my specific problem, while i tried to be very careful while creating the generalized versions, i could have made mistakes.**

**I am not responsible for any damage caused by my scripts when you were using them irresponsibly, and did not understand them in detail**

---
## Information about the scripts  ##

### oc-generate-share-keys.php ###

**Purpose** : This script generates a set of share-keys and the keyfile for an owncloud serverside-encrypted file. The only required commandline argument is the so called objectname, which is a relative pathname of the serverside-encrypted file.

The funtionality of this script is limited to generate new set of key files of already shared files, where the sharing process failed for some user, who is marked to be migrated, leaving the key files inconsistent state, and at least the owner can acccess the file without problems. This script does not share, encrypt, or create new encryption key for a file, also it does not update any database information that may be required for the flawless operation of OwnCloud.

**Details**

The relative pathname has to be the path as it is seen in the shareowner user's owncloud web account, an example:

* The file birthday-party-guestlist.odt is in the shareowner user's "\Documents\Reports\ThisYear\" folder in the web account
* The data directory of owncloud is **/srv/owncloud-data**
* The share owner user is called **sharemaster**
* Based on the above the file is located in **/srv/owncloud-data/sharemaster/files/Documents/Reports/ThisYear/birthday-party-guestlist.odt**

The relative pathname that my script would require is **Documents/Reports/ThisYear/birthday-party-guestlist.odt**

**WARNING: The script assumes that the topmost folder of the owner is shared, and is not prepared for the case a subdirectory has been shared**

Inside the script you have to set following variables:

 * SHAREOWNER - the username of the owner of the shared file
 * OWNERPASSWORD - the passwor of the sharing user
 * OCDATADIR - the full path to the OwnCloud datadir without trailing slash
 * myAllowUsers - array of usernames who were specified in the OwnCloud webinterface to have access to the file including the owner
 * the script needs to load a class from the Owncloud Encryption APP so it should be placed it in a folder in the web-root of OwnCloud, if you place it in some other location, you have to adjust the path of the *require_once* function.


### oc-sharekey-robot.sh ###

**Purpose**: This bash script serves as a wrapper for **oc-generate-share-keys.php**, to allow the regeneration of key sets for all files of a share.

**Details**

All settings have to be defined inside the script, the script does not check for the presence of any shell commands that are used, but if you have owncloud installed, and maybe already used it's CLI command **occ**, you should be ok.
The logic (steps) of the script is:

1. create the base work directory for the resulting key sets if needed
2. change to the top directory of the share
3. finds all files
4. for every file found
  1. if the owner's shareKey is available
    1. create the folderstructure for the keyfile if needed **since the oc-generate-share-keys.php does the same, this may be removed later**
    2. create the folderstructure for the share-keys if needed **since the oc-generate-share-keys.php does the same, this may be removed later**
    3. change to the starting directory, and call oc-generate-share-keys.php with the "objectname" as parameter, and record it's response in a logfile, and also display it on the console
  2. if the owner's shareKey is missing, record the "objectname" in a separate logfile
5. in the end, change the filesystem ownership recursively for the whole work directory to UID/GID 65534 (nobody) **This can differ from system to system, it even may not be required at all**
6. to be sure change to the starting directory again ;)

Inside the script you have to set following variables:
 * SHARENAME - the name of the root-folder that is being shared by the owner
 * SHAREOWNER - the username of the owner of the shared file
 * OCDATADIR - the full path to the OwnCloud datadir without trailing slash


[![Analytics](https://ga-beacon.appspot.com/UA-1719348-3/gnanet/misc-scripts/owncloud)](https://github.com/igrigorik/ga-beacon)
