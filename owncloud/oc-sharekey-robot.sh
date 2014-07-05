#!/bin/bash
SHARENAME="the folder that is being shared"
SHAREOWNER="the user who owns the share"
$OCDATADIR="/path-to-oc-datadir-no-trailing-slash"
FILEBASE=${OCDATADIR}/${SHAREOWNER}/files/${SHARENAME}
KEYBASE=${OCDATADIR}/${SHAREOWNER}/files_encryption/keyfiles/${SHARENAME}
SHAREBASE=${OCDATADIR}/${SHAREOWNER}/files_encryption/share-keys/${SHARENAME}
oldpwd=`pwd`
WORKDIR="${oldpwd}/work-${SHAREOWNER}/files_encryption"

if [ ! -d ${WORKDIR} ]
then
mkdir -p ${WORKDIR}
fi

cd ${FILEBASE}
find ./ -type f | sed -e "s/^..//g " | while read object
do

if [ -f "${SHAREBASE}/${object}.${SHAREOWNER}.shareKey" ]
then
        opath="${SHARENAME}/`dirname "${object}"`"
        echo $opath
        if [ ! -d "${WORKDIR}/keyfiles/${opath}" ]
        then
        mkdir -p "${WORKDIR}/keyfiles/${opath}"
        fi

        if [ ! -d "${WORKDIR}/share-keys/${opath}" ]
        then
        mkdir -p "${WORKDIR}/share-keys/${opath}"
        fi

        cd ${oldpwd}
        php ./oc-generate-share-keys.php "${SHARENAME}/${object}" 2>&1 | tee -a ${oldpwd}/${SHARENAME}-gen.log

else
        echo "${SHARENAME}/${object}" >> ${oldpwd}/${SHARENAME}-sharekey-none.log
fi

done

chown 65534:65534 ${WORKDIR} -R
cd ${oldpwd}
