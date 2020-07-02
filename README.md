# book-recommender
[![forthebadge made-with-python](http://ForTheBadge.com/images/badges/made-with-python.svg)](https://www.python.org/)


## Overview
The recommender system uses the CMU Book Summary Dataset.<br>
The recommender system API was developed in Python Flask.<br>
The webpage backend and frontend was developed in PHP.<br>
The book list is stored in ClearDB MySQL.<br>

This book recommender system has five main features:<br>
[1] Add book title to list<br>
[2] Delete book title from list<br>
[3] Group list contents<br>
[4] Export list contents to CSV/XML<br>
[5] Get book recommendations from list contents<br>
<br>
[1] Add book title to list<br>
![Add book](https://raw.githubusercontent.com/leeseojun17/book-recommender/master/img/addbook.jpg)<br>
The add new book section features a text field that can handle minimal typographical errors. It uses Damerau-Levenshtein distance as a metric when taking into consideration the nearest match from the summary dataset.<br><br>
[2] Delete book title from list<br>
![Delete book](https://raw.githubusercontent.com/leeseojun17/book-recommender/master/img/delete.jpg)<br>
The books in the list can be individually deleted from the database.<br><br>
[3] Group list contents<br>
![Group filter](https://raw.githubusercontent.com/leeseojun17/book-recommender/master/img/groups.jpg)<br>
The book list can be filtered into groups/genres. The groups were produced via Latent Dirichlet Allocation on the bag of words provided by the summaries from the dataset.<br><br>
[4] Export list contents to CSV/XML<br>
![Export file](https://raw.githubusercontent.com/leeseojun17/book-recommender/master/img/export.jpg)<br>
The entire book list can be exported to CSV or XML or just its title column or its author column.<br><br>
[5] Get book recommendations from list contents<br>
![Recommend books](https://raw.githubusercontent.com/leeseojun17/book-recommender/master/img/rec.jpg)<br>
A selected book from the list can be asked for recommendations and the top five results would be displayed in a separate section. Each recommendation can be added to the existing book list. The recommendations are obtained via cosine similarity of the vectorized version of bag of words from the summary dataset.

## Getting Started
### Built with
* [Python 3.7 or above](https://www.python.org/downloads/) - API via Flask<br>
* [PHP 7.4 or above](https://www.apachefriends.org/download.html) - Frontend and backend of webpage<br>

### Installing
To get started, install Python 3.7 and PHP 7.4. Place the files in the proper environments.<br>
Install the dependencies for Python by running:<br>
```
pip install -r requirements.txt
```
Setup your own MySQL database. Create a table named `yaraku_tbl` with columns Title, Author, and Groups.<br>
Update the MySQL credentials in `dbcontroller.php`.<br>
