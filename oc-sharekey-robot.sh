#!/bin/bash
SHARENAME="the folder where "
SHAREOWNER="the user who owns the share"
FILEBASE=/the-owncloud-datadir/${SHAREOWNER}/files/${SHARENAME}
KEYBASE=/the-owncloud-datadir/${SHAREOWNER}/files_encryption/keyfiles/${SHARENAME}
SHAREBASE=/the-owncloud-datadir/${SHAREOWNER}/files_encryption/share-keys/${SHARENAME}
oldpwd=`pwd`

if [ ! -d ./work ]
then
mkdir ./work
fi


cd ${FILEBASE}

find ./ -type f | sed -e "s/^..//g " | while read object
do

if [  -f  "${SHAREBASE}/${object}.${SHAREOWNER}.shareKey" ]
then

        opath="${SHARENAME}/`dirname "${object}"`"
        echo $opath
        if [ ! -d "${oldpwd}/work/key/${opath}" ]
        then
        mkdir -p "${oldpwd}/work/key/${opath}"
        fi

        if [ ! -d "${oldpwd}/work/share/${opath}" ]
        then
        mkdir -p "${oldpwd}/work/share/${opath}"
        fi

        cd ${oldpwd}
        php ./oc-generate-share-keys.php "${SHARENAME}/${object}" 2>&1 | tee -a ${SHARENAME}-gen.log

else
        echo "${SHARENAME}/${object}" >> ${SHARENAME}-sharekey-none.log
fi

done

cd ${oldpwd}
