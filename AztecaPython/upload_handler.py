#!D:\anaconda3\python.exe
#
# POST cgi handler for handling uploaded files.
# This script inspects file is in JPG

# Import modules for CGI handling 
import os, cgi, cgitb, imghdr
from PIL import Image

from PIL import ImageOps
from PIL import ImageFilter
from PIL.ExifTags import TAGS, GPSTAGS

def get_exif_data(image):
    """Returns a dictionary from the exif data of an PIL Image item. Also converts the GPS Tags"""
    exif_data = {}
    info = image._getexif()

    if info:
        for tag, value in info.items():
            decoded = TAGS.get(tag, tag)
            if decoded == "GPSInfo":
                gps_data = {}
                for t in value:
                    sub_decoded = GPSTAGS.get(t, t)
                    gps_data[sub_decoded] = value[t]
                exif_data[decoded] = gps_data
            else:
                exif_data[decoded] = value

    return exif_data

def _get_if_exist(data, key):
    if key in data:
        return data[key]

    return None

def _split(value):
    d0 = value[0][0]
    d1 = value[0][1]
    d = float(d0) / float(d1)

    m0 = value[1][0]
    m1 = value[1][1]
    m = (m0) / float(m1)

    s0 = value[2][0]
    s1 = value[2][1]
    s = float(s0) / float(s1)
    return (int(d),int(m),int(s))

cgitb.enable()

#--initialise variables--#
form = cgi.FieldStorage()# A nested FieldStorage instance holds the file


message = ""
if(form.getfirst("fileToUpload","")):
    fileItem = form['fileToUpload']
    fileName = form.getfirst("file_name","")
    filter_mode = form.getfirst("optradio","0")
    read_exif_mode = form.getfirst("optcheck","0")

    # strip leading path from file name to avoid directory traversal attacks
    fileFormat = imghdr.what(None,fileItem.value)
    if fileFormat:
        if fileFormat == 'jpeg':
            original_path = '../../tmpmedia/original/'+fileName+".jpg"
            originalIm = open(original_path , 'wb').write(fileItem.file.read())
            #Original Image
            im = Image.open(original_path)
            #Resize and fit original Image to 512 X 512
            size =(512,512)
            im1 = im.thumbnail(size, Image.ANTIALIAS)
            im1 = ImageOps.fit(im,size,Image.ANTIALIAS)


            if (filter_mode =="1"):

                box=(50,50,450,450)
                region = im1.crop(box)# region of 400 X 400
                im1 = im1.filter(ImageFilter.EMBOSS)
                im1.paste(region,box)

            if (filter_mode =="2"):
                box=(50,50,450,450)
                region = im1.crop(box)# region of 400 X 400
                im1 = im1.filter(ImageFilter.FIND_EDGES)
                im1.paste(region,box)

            if (filter_mode =="3"):
                box=(50,50,450,450)
                region = im1.crop(box)# region of 400 X 400
                im1 = im1.filter(ImageFilter.BLUR)
                im1.paste(region,box)

            if (read_exif_mode=="1"):

                exif_data = get_exif_data(im)
                if "GPSInfo" in exif_data:
                    gps_info = exif_data["GPSInfo"]
                    gps_latitude = _get_if_exist(gps_info, "GPSLatitude")
                    gps_latitude_ref = _get_if_exist(gps_info, 'GPSLatitudeRef')
                    gps_longitude = _get_if_exist(gps_info, 'GPSLongitude')
                    gps_longitude_ref = _get_if_exist(gps_info, 'GPSLongitudeRef')
                    gps_altitude = _get_if_exist(gps_info, 'GPSAltitude')

                    lat1,lat2,lat3 = _split(gps_latitude)
                    lon1,lon2,lon3 = _split(gps_longitude)
                    message += '<div>latitude : ' + str(lat1) + "&deg  " + str(lat2)+ "&#8242 " +str(lat3) + "&#8243 " + gps_latitude_ref+'</div>'
                    message += '<div>longitude : ' + str(lon1) + "&deg  " + str(lon2)+ "&#8242 " +str(lon3) + "&#8243 " + gps_longitude_ref+'</div>'


                else:
                    message += '<div>No GPS EXIF data!</div>'



            im1.save(original_path,"JPEG")

            message += '<br>The file is uploaded successfully with selected mode and will be saved as original image.<br><br>' \
                                               'Image created :<div><img src=' + original_path + '></div>'
        else:
            message += 'The file uploaded is of type "' + fileFormat + '". Only JPEG is allowed to upload'
    else:
        message += "The file uploaded is of unsupported image type. Only JPEG is allowed to upload"
else:
    message += 'No file is uploaded'

###--------Display in web --------------###
print("Content-type:text/html\r\n\r\n")
print("<html><head>")
print("<title>Result</title>")
print("</head><body>")

#print message
print("<h2>%s</h2>" % message )
print(read_exif_mode)
print ("</body></html>")