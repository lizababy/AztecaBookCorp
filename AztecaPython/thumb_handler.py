#!D:\anaconda3\python.exe
#
# POST cgi handler for handling uploaded files.
# This script inspects file is in JPG

# Import modules for CGI handling 
import os, cgi, cgitb, imghdr
from PIL import Image
cgitb.enable()

###------function to create thumbnail----###
def create_thumbnail_fun(file_name):
    size = (128, 128)
    file = file_name + ".thumbnail.jpg"
    original_path = '../../tmpmedia/original/'+file_name +".jpg"
    try:
        im = Image.open(original_path)
        im.thumbnail(size, Image.ANTIALIAS)
        file_thumb_path = '../../tmpmedia/thumbnail/' + file
        im.save(file_thumb_path, "JPEG")
        return file_thumb_path
    except IOError:
        print("cannot create thumbnail for",original_path)


form = cgi.FieldStorage()# A nested FieldStorage instance holds the file

if form.getfirst("fileToThumb",""):#create thumb

    fileName = form.getfirst("fileToThumb","")
    thumbPath = create_thumbnail_fun(fileName)
    message = "<h2>created thumb is: </h2><img src=" + thumbPath + ">"

else:
    message = 'No image file is uploaded'


###--------Display in web --------------###
print("Content-type:text/html\r\n\r\n")
print("<html><head>")
print("<title>Result</title>")
print("</head><body>")

#print message
print("<h2>%s</h2>" % message)

print ("</body></html>")