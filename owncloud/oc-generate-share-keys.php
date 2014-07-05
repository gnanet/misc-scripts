<?php


// This script is meant to be called from CLI

// The script generates a new set of shareKeys and the encrypted file-key for an encrypted shared file
// if the share owner already has its


if ($argc<2) {
fwrite(STDERR, "Object needed\n");
exit(1);
}


// I placed the script in a subfolder of owncloud web-root
require_once '../apps/files_encryption/lib/crypt.php';


$startdir=$_SERVER['PWD'];
$SHAREOWNER = 'masteruser';
$OWNERPASSWORD = 'somepassword';
$OCDATADIR="/path-to-oc-datadir-no-trailing-slash";
$WORKDIR=$startdir . "/work-" . $SHAREOWNER ."/files_encryption";

$myAllowUsers = array($SHAREOWNER,"user1", "user2", "user3", "user4");


// the file for we need new keys has to be relative to the "files" folder of the share-owner
$OBJECTNAME = $argv[1];

if (!is_file($OCDATADIR."/".$SHAREOWNER."/files_encryption/keyfiles/".$OBJECTNAME.".key")) {
	fwrite(STDERR, 'No keyfile found for Object, cannot continue');
	exit(1);
}


$FILENAME = basename($OBJECTNAME);
$OPATH = dirname($OBJECTNAME);

// first get share owners private key and decrypt it
$encryptedUserKey = file_get_contents("$OCDATADIR/$SHAREOWNER/files_encryption/$SHAREOWNER.private.key");
$decryptedUserKey = OCA\Encryption\Crypt::decryptPrivateKey($encryptedUserKey, $OWNERPASSWORD);

// now we need to decrypt the file-key, therefore we use the private key and the share key
$shareKey = file_get_contents("$OCDATADIR/$SHAREOWNER/files_encryption/share-keys/$OBJECTNAME.$SHAREOWNER.shareKey");
$encryptedKeyfile = file_get_contents("$OCDATADIR/$SHAREOWNER/files_encryption/keyfiles/$OBJECTNAME.key");
$decryptedKeyfile = OCA\Encryption\Crypt::multiKeyDecrypt($encryptedKeyfile, $shareKey, $decryptedUserKey);

// then we get the users public key
$userPubKeys = array();
foreach ( $myAllowUsers as $myAllowUser ) {
        $userPubKeys[$myAllowUser] = file_get_contents("$OCDATADIR/public-keys/" . $myAllowUser . '.public.key');
}

// generating the new keys
$multiEncKey = OCA\Encryption\Crypt::multiKeyEncrypt($decryptedKeyfile, $userPubKeys);

$newshareKeys = $multiEncKey['keys'];
$newKeyfile = $multiEncKey['data'];


// create recursive work directory structure for keyfiles and share-keys if needed
if (!is_dir($WORKDIR . "/share-keys/" . $OPATH)) {
		if (!mkdir($WORKDIR . "/share-keys/" . $OPATH, 0770, true)) {
			fwrite(STDERR, 'Failed to create folders...');
			exit(1);
			}
}
if (!is_dir($WORKDIR . "/keyfiles/" . $OPATH)) {
		if(!mkdir($WORKDIR . "/keyfiles/" . $OPATH, 0770, true)) {
			fwrite(STDERR, 'Failed to create folders...');
			exit(1);
		}
}


// storing the key files
foreach ($newshareKeys as $userId => $newshareKey) {
        file_put_contents($WORKDIR . "/share-keys/" . $OBJECTNAME . "." . $userId . ".shareKey", $newshareKey);
}

file_put_contents($WORKDIR . "/keyfiles/" . $OBJECTNAME . ".key", $newKeyfile);

fwrite(STDERR, "Done $FILENAME\n");

