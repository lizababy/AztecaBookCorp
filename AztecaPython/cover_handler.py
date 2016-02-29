#!D:\anaconda3\python.exe
#
# POST cgi handler for handling uploaded files.
# This script inspects file is in JPG

# Import modules for CGI handling 
import os, cgi, cgitb, imghdr
from PIL import Image
from PIL import ImageDraw
from PIL import ImageFont
cgitb.enable()

###------function to create cover image----###

def create_cover(file_name, cover_text_f1, cover_text_f2, cover_text_s, cover_text_b):

    original_path = '../../tmpmedia/original/'+file_name+".jpg"
    try:
        im_front = Image.open(original_path).convert('RGBA')
        front_w, front_h = im_front.size
        im_back = Image.open(original_path).convert('RGBA')
        back_w, back_h = im_back.size

        bg_im =Image.new('RGBA',(1104,512),(0,0,0,255))
        bg_w, bg_h = bg_im.size

        im_spine =Image.new('RGBA',(512,80),(117,15,46,255))
        spine_h, spine_w = bg_im.size

        offset_b = (0,0)
        offset_s = (front_w,0)
        offset_f = ((front_w + 80),0)

        draw_front = ImageDraw.Draw(im_front)
        draw_back = ImageDraw.Draw(im_back)
        x_center = front_w/2

        #--line 1 front--#
        font_size = 50
        font = ImageFont.truetype("arial.ttf",font_size)
        f_w,f_h = font.getsize(cover_text_f1)
        x_f1 = int(x_center - f_w/2)
        y_f1 = front_h/2 - f_h
        draw_front.text((x_f1,y_f1),cover_text_f1,font=font, fill = (194,62,102, 255))#red

        #--line 2 front---#
        font_size = 25
        f_w,f_h = font.getsize(cover_text_f2)
        x_f2 = int(x_center - f_w/2)
        y_f2 = front_h*.9
        font = ImageFont.truetype("arial.ttf",font_size)
        draw_front.text((x_f2,y_f2),cover_text_f2,font=font, fill = (62, 97, 194, 255))#blue

        #--line 1 spine---#

        draw_spine = ImageDraw.Draw(im_spine)

        x_s = spine_w*.07
        y_s = spine_h*.02
        font_size = 40
        font = ImageFont.truetype("arial.ttf",font_size)
        draw_spine.text((x_s,y_s),cover_text_s,(255,255,0),font=font)#yellow
        im_spine = im_spine.rotate(270,expand=1)
        draw_spine = ImageDraw.Draw(im_spine)
        logo = Image.open("../includes/images/logo_s.png")
        im_spine.paste(logo,(0,spine_w-logo.size[1]-20))

        font = ImageFont.truetype("arial.ttf",15)
        draw_spine.text((5,spine_w-logo.size[1]-font_size/2 -20),"ABC Corp",(255,255,255),font=font)#white

        #--line 1 back---#
        x_b = back_w*.07
        y_b = back_h*.9
        font_size = 30
        font = ImageFont.truetype("arial.ttf",font_size)
        draw_back.text((x_b,y_b),cover_text_b,font=font, fill = (255, 255, 255, 255))#white

        file_cover_path = '../../tmpmedia/cover/' + file_name +  ".cover.jpg"

        bg_im.paste(im_front, offset_f)
        bg_im.paste(im_spine, offset_s)

        bg_im.paste(im_back, offset_b)

        bg_im.save(file_cover_path, "JPEG")
        return file_cover_path
    except IOError:
        print("cannot create cover for",original_path)


form = cgi.FieldStorage()# A nested FieldStorage instance holds the file



if form.getfirst("fileToCover",""):
    fileName = form.getfirst("fileToCover","")
    cover_text_f1 = form.getfirst("cover_text_f1","").title()
    cover_text_f2 = form.getfirst("cover_text_f2","").title()
    cover_text_s = form.getfirst("cover_text_s","").title()
    cover_text_b = form.getfirst("cover_text_b","").title()
    coverPath = create_cover(fileName,cover_text_f1,cover_text_f2,cover_text_s,cover_text_b)
    if(coverPath):
        message = "<h2>created cover image is: </h2><img src=" + coverPath+">"
    else:
        message = 'cover not created'
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