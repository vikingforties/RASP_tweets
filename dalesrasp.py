#   Stratus result... http://rasp.mrsap.org/cgi-bin/get_rasp_blipspot.cgi?region=Tuesday&grid=d2&day=0&lat=54.266678&lon=-2.200172&width=2000&height=2000&linfo=1&param=
#   Stars graphic too http://app.stratus.org.uk/blip/blip_stars.php?region=Tuesday&i=1111&k=1123   add to tweet!!
#   Usual output #DalesRASP Tue-0.0* Wed-0.1* Thu-0.0* Fri-0.0* Sat-0.0* Sun-0.0* Mon-0.0* #Paragliding #YorkshireDales #forecast #RASP http://app.stratus.org.uk/blip/blip_stars.php?region=Tuesday&i=1111&k=1123
import requests
from datetime import date
import tweepy

weekdays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
weekdays += weekdays
weekshort = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]
weekshort += weekshort
weekahead = []
weekahdsht = []
go_picture = False

for i in range(7):
    weekahead.append(weekdays[date.today().weekday() + i])
    weekahdsht.append(weekshort[date.today().weekday() + i])

forecast_stars = "#DalesRASP "  # Change this to match location
for day in weekahead:
    r = requests.get('http://rasp.mrsap.org/cgi-bin/get_rasp_blipspot.cgi?region=' + day + '&grid=d2&day=0&lat=54.266678&lon=-2.200172&width=2000&height=2000&linfo=1&param=')
    response = r.text.split('\n')
    while response[-1] == "":
        response.pop()
    starsline = response.pop().split()
    while "Stars" in starsline: starsline.remove("Stars")
    while '-' in starsline: starsline.remove('-')
    starsfloat = [float(i) for i in starsline]
    forecast_stars += (weekahdsht[weekahead.index(day)] + "-" + str(round(max(starsfloat), 1)) + "* ")
    if max(starsfloat) > 0.9 and day ==  weekdays[date.today().weekday()]:
        go_picture = True

forecast_stars += ("#Paragliding #YorkshireDales #forecast #RASP " + 'http://app.stratus.org.uk/blip/blip_stars.php?region=' + weekdays[date.today().weekday()] + '&i=1111&k=1123')  # Change this to match location. 
# http://app.stratus.org.uk/blip/blip_stars.php?region=Tuesday&i=1111&k=1123

access_token = "xxxxxxx"
access_token_secret = "xxxxxxx"
consumer_key = "xxxxxxx"
consumer_secret = "xxxxxxx"

auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_token, access_token_secret)
# Creation of the actual interface, using authentication
api = tweepy.API(auth) 
# Sample method, used to update a status

if go_picture:
    graph_url = 'http://app.stratus.org.uk/blip/blip_stars.php?region=' + weekdays[date.today().weekday()] + '&i=1111&k=1123'
    graph_file = "DalesStars.jpg"
    f = requests.get(graph_url)
    if f.status_code == 200:
        with open(graph_file, 'wb') as local_file:
                for chunk in f.iter_content(chunk_size=128):
                    local_file.write(chunk)
    api.update_with_media('DalesStars.jpg', forecast_stars)
else:
    api.update_status(forecast_stars)

# Std out log entry to indicate success
print(str(date.today()) + " " + forecast_stars)
