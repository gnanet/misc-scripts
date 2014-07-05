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

$SHAREOWNER = 'masteruser';
$OWNERPASSWORD = 'somepassword';
$myAllowUsers = array($SHAREOWNER,"user1", "user2", "user3", "user4");

// the file for we need new keys has to be relative to the "files" folder of the share-owner
$FILEFULLNAME = $argv[1];
$FILENAME = basename($FILEFULLNAME);

$OCDATADIR="/path-to-oc-datadir-no-trailing-slash";


// first get share owners private key and decrypt it
$encryptedUserKey = file_get_contents("$OCDATADIR/$SHAREOWNER/files_encryption/$SHAREOWNER.private.key");
$decryptedUserKey = OCA\Encryption\Crypt::decryptPrivateKey($encryptedUserKey, $OWNERPASSWORD);

// now we need to decrypt the file-key, therefore we use the private key and the share key
$shareKey = file_get_contents("$OCDATADIR/$SHAREOWNER/files_encryption/share-keys/$FILEFULLNAME.$SHAREOWNER.shareKey");
$encryptedKeyfile = file_get_contents("$OCDATADIR/$SHAREOWNER/files_encryption/keyfiles/$FILEFULLNAME.key");
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


// TODO migrate the recursive folder creation from the shell script to avoid problems caused by different folder settings
// 


// storing the key files
foreach ($newshareKeys as $userId => $newshareKey) {
        file_put_contents("./work/share-keys/". $FILEFULLNAME . "." . $userId . ".shareKey", $newshareKey);
}

file_put_contents("./work/keyfiles/" . $FILEFULLNAME . ".key", $newKeyfile);

fwrite(STDERR, "Done $FILENAME\n");

