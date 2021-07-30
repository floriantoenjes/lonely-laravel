# Lonely App

This is an application for alleviating loneliness, which I developed for practice purposes.

## Functionality

* After registration users can sign in, customize their profile, fill out a search form, 
  and mark themselves as lonely.
* Users can see other lonely people as well as activities to participate in on a map, narrowed down
  by their search parameters.
* Users can create new activities or start chatting with other users.
* Users are notified instantly once someone sends a chat message either directly or in a acitvity 
they participate in.
  
## Technical Details

* The backend is written in PHP and the Laravel 7 framework.
* MySQL is used to persist the data.
* The frontend is fully written in VueJS and is styled using the Tailwind CSS library.
* Frontend and backend communicate directly without a REST api. This is done by using Inertia.js which
is able to hydrate backend data straight into the properties of Vue components.
* Instant news communication is assured by using Websockets.   


## Images

### Dashboard
![](readme_images/Screenshot%20from%202021-07-30%2018-41-27.png)

### A Member Chat
![](readme_images/Screenshot%20from%202021-07-30%2018-42-50.png)

### Activity Overview

![](readme_images/Screenshot%20from%202021-07-30%2018-44-23.png)
