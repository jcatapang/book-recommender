#!/usr/bin/env python
# coding: utf-8

# In[1]:


import pickle
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.metrics.pairwise import cosine_similarity
#from sklearn.neighbors import NearestNeighbors
from sklearn.preprocessing import normalize
from jellyfish import damerau_levenshtein_distance


# In[2]:


try:
    with open('book_summaries.pkl', 'rb') as f:
        cmu_books = pickle.load(f)
    print("book_summaries.pkl has been loaded successfully")
except:
    print("book_summaries.pkl doesn't exist")
    exit()


# In[3]:


cmu_books = cmu_books.drop_duplicates(subset='BOW', keep="last")
cmu_books.tail()


# In[4]:


# Passing the bag of words to a count vectorizer
'''cv = CountVectorizer()
cv_bow = cv.fit_transform(cmu_books['BOW'])

with open('cv_bow.pkl', 'wb') as f:
    pickle.dump(cv_bow, f)
'''
try:
    with open('cv_bow.pkl', 'rb') as f:
        cv_bow = pickle.load(f)
    print("cv_bow.pkl has been loaded successfully")
except:
    print("cv_bow.pkl doesn't exist")
    exit()

# For title matching later on
idx = pd.Series(cmu_books.index)


# In[5]:


def get_cosine_similarity(cv_bow, i):
    cs = cosine_similarity(cv_bow[i], cv_bow).flatten()
    print(cs.shape)
    return cs


# In[6]:


def get_damlev(book1, book2):
    return damerau_levenshtein_distance(book1,book2)


# In[7]:


def get_n_recs(book_title, n):
    global idx
    book_recommendations = list()
    
    try:
        if "the "+book_title.lower() in map(str.lower, idx):
            book_title = "The "+book_title
    except:
        book_title = book_title
    
    # Getting the index of the book title from the list of books that is exactly the entered title
    # or closely resembles the entered title
    edit_scores = list()
    for i in idx:
        damlev_score = get_damlev(book_title.lower(), i.lower())
        edit_scores.append(damlev_score)
    
    min_score_idx = edit_scores.index(min(edit_scores))
    print(min_score_idx)
    title_idx = idx[min_score_idx]
    author = cmu_books['Author'][min_score_idx]
    print(title_idx, author)

    # Getting the scores sorted by relevance
    cosine_scores = pd.Series(get_cosine_similarity(cv_bow, min_score_idx)).sort_values(ascending = False)

    # Getting the indices of the n most similar books
    top_n = list(cosine_scores.iloc[1:n+1].index)
    
    # Recommending the top n similar titles
    for rec_title in top_n:
        rec_author = cmu_books['Author'][rec_title]
        if type(rec_author) != str:
            rec_author = "Unknown"
        book_recommendations.append([list(cmu_books.index)[rec_title], rec_author])
        
    return book_recommendations

