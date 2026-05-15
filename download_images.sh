#!/bin/bash
# سكريبت لنسخ صور Active Storage من السيرفر

# على السيرفر، نفذ هذا الأمر:
# 1. اذهب لمجلد المشروع
cd /path/to/white-style-project

# 2. اعثر على مجلد التخزين
# عادة يكون في storage/
find storage -type f -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.gif"

# 3. انسخ جميع الصور
mkdir -p ~/white_style_images
cp storage/**/* ~/white_style_images/

# 4. اضغط المجلد
tar -czf white_style_images.tar.gz ~/white_style_images/

# 5. حمّل الملف المضغوط على جهازك
# scp user@server:~/white_style_images.tar.gz D:/localProjects/whiteStyle/
