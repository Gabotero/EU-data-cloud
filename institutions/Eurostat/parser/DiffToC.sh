#!/bin/bash
java -cp "./build:./lib/*" -Xmx512M org.deri.eurostat.toc.DiffToC -i "/downloadZip/ToC/table_of_contents.xml" -o "/downloadZip/ToC/" -f "/downloadZip/updateLogs/" -z "/downloadZip/tempZip/" -t "/downloadZip/tempData/" -s "/downloadZip/data/" -d "/downloadZip/dsd/" -l "/downloadZip/log/" -p "/downloadZip/original-data/" -r "/downloadZip/raw-data/"