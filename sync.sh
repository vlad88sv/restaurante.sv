#!/bin/bash
rsync --compress-level=9 --exclude '*.psd' --exclude '*.xcf' --exclude '*.git' -a --progress /home/vladimir/Documentos/Codigo/restaurante.sv/  lapizzeria@volcan.zapto.org:/var/www/
