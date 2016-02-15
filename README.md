# kovaciny.com
Private hand-coded forum site, active since 2005. I took over maintenance in 2013. This project gave me a lot of hands-on experience with full stack web development and debugging other people's code.

## Development environment
The code base is about 1800 lines of PHP and Javascript. I brought the site under version control, separated development and production environments, and created a [Gradle build script](build.gradle) to automate Javascript linting and site deployment. 

## Code maintenance
As a real-world project, kovaciny.com gave me experience with some common Web issues:  

* time zone problems  
* cross browser differences  
* scaling for mobile  
* parsing error logs  
* changing hosts  
* Javascript namespaces  
* caching problems  

It's also valuable experience to return to your own code several years later when you have to debug.

## Features and improvements
* Added indexes and rewrote queries to drop front page query time from 1.5 seconds to 0.3 seconds
* Added full-text search with results highlighting:  
![Full-text search screenshot](/../screenshots/screenshots/search.png?raw=true)
* Show user stats with Google Charts API
* Gradual refactoring with Promises and Ajax to make the site more responsive, creating some API endpoints, and making the Javascript more object-oriented.

This repo is not live. It's a snapshot pulled from the Mercurial repository, with security-related code removed.