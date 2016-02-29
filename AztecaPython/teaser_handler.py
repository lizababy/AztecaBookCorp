#!D:\anaconda3\python.exe
#
# POST cgi handler for handling uploaded files.
# This script inspects file is in JPG

# Import modules for CGI handling 
import os, cgi, cgitb, imghdr
from PIL import Image
from PIL import ImageDraw
from PIL import ImageFont
from PIL import ImageOps
cgitb.enable()

###------function to create Teaser image----###

def create_teaser(file_name, teaser_text1, teaser_text2, teaser_text3,teaser_text_c):

    original_path = '../../tmpmedia/original/'+file_name+".jpg"
    try:
        im = Image.open(original_path).convert('RGBA')
        width, height = im.size

        bg_im =Image.new('RGBA',(560,560),(0,0,0,255))
        bg_w, bg_h = bg_im.size
        offset = (int((bg_w-width)/2),int((bg_h-height)/2))
        draw = ImageDraw.Draw(im)
        x_center = width/2
        #--line 1--#

        font_size = 40
        font = ImageFont.truetype("arial.ttf",font_size)
        f_w,f_h = font.getsize(teaser_text1)
        x_f1 = int(x_center - f_w/2)
        y_f1 = height*.07
        draw.text((x_f1,y_f1),teaser_text1,font=font, fill = (255, 74, 49, 255))#orange

        #--line 2---#
        font_size = 50
        font = ImageFont.truetype("arial.ttf",font_size)
        f_w,f_h = font.getsize(teaser_text2)
        x_f2 = int(x_center - f_w/2)
        y_f2 = height/2 - f_h
        draw.text((x_f2,y_f2),teaser_text2,font=font, fill = (194,62,102, 255))#red

        #--line 3---#
        font_size = 25
        f_w,f_h = font.getsize(teaser_text3)
        x_f3 = int(x_center - f_w/2)
        y_f3 = height*.9
        font = ImageFont.truetype("arial.ttf",font_size)
        draw.text((x_f3,y_f3),teaser_text3,font=font, fill = (62, 97, 194, 255))#blue

         #-- watermark text---#
        file_teaser_path = '../../tmpmedia/teaser/' + file_name +  ".teaser.jpg"
        if(teaser_text_c):
            watermark_layer = Image.new("RGBA",(width, height), (255,255,255,30))
            watermark_draw = ImageDraw.Draw(watermark_layer)
            font = ImageFont.truetype("arial.ttf",70)
            f_w,f_h = font.getsize(teaser_text_c)
            watermark_draw.text((width-f_w,height-f_h),teaser_text_c,font=font,fill=(0,0,0,100))
            watermark_layer = watermark_layer.rotate(270)
            im = Image.composite(watermark_layer,im,watermark_layer)

        bg_im.paste(im, offset)
        bg_im.save(file_teaser_path, "JPEG")
        return file_teaser_path
    except IOError:
        print("cannot create teaser for",original_path)


form = cgi.FieldStorage()# A nested FieldStorage instance holds the file



if form.getfirst("fileToTeaser",""):
    fileName = form.getfirst("fileToTeaser","")
    originalPath = '../../tmpmedia/original/'+fileName
    teaserText1 = form.getfirst("teaser_text1","").title()
    teaserText2 = form.getfirst("teaser_text2","").title()
    teaserText3 = form.getfirst("teaser_text3","").title()
    teaser_text_c = form.getfirst("teaser_text_c","").title()
    teaserPath = create_teaser(fileName,teaserText1,teaserText2,teaserText3,teaser_text_c)
    if(teaserPath):
        message = "<h2>created teaser image is: </h2><img src=" + teaserPath + ">"
    else:
        message = 'teaser not created'
else:
    message = 'No file is uploaded'

###--------Display in web --------------###
print("Content-type:text/html\r\n\r\n")
print("<html><head>")
print("<title>Result</title>")
print("</head><body>")

#print message
print("<h2>%s</h2>" % message)

print ("</body></html>")