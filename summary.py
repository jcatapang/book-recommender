#!/usr/bin/env python
# coding: utf-8

# In[1]:


import pandas as pd
import numpy as np
import os
import string
import pickle
import sys
from sklearn.preprocessing import LabelEncoder


# In[2]:


import nltk
nltk.download('stopwords')
from nltk.corpus import stopwords
stop = stopwords.words('english')


# In[3]:


# Reading the dataset and assigning labels for each column
cmu_books = pd.read_csv("booksummaries.txt", sep='\t', names=['Wiki_ID', 'Freebase_ID', 'Title', 'Author', 'Pub_Date', 'Genre', 'Plot'])


# In[4]:


# Dropping the rows where there is no plot and reset the indices
cmu_books.dropna(subset = ['Plot'], inplace = True)
cmu_books.reset_index(inplace = True, drop = True)


# In[5]:


# Dropping column with insufficient data
cmu_books.drop(['Wiki_ID', 'Pub_Date', 'Freebase_ID', 'Genre'], axis=1, inplace = True)


# In[6]:


'''
Removing punctuations for the plot by checking for all the punctuations contained in the string library
'''
def remove_punctuations(text):
    for punctuation in string.punctuation:
        if punctuation == "-": # dashed compound words should be split
            text = text.replace(punctuation, ' ')
        else:
            text = text.replace(punctuation, '')
    return text


# In[7]:


# Converting the plot details into lowercase and stripping them of punctuations
cmu_books['Author (Raw)'] = cmu_books['Author'].copy()
cmu_books[['Author', 'Plot']] = cmu_books[['Author', 'Plot']].apply(lambda x: x.astype(str).str.lower())
cmu_books['Title'] = cmu_books['Title'].apply(remove_punctuations)
cmu_books['Author'] = cmu_books['Author'].apply(remove_punctuations)
cmu_books['Plot'] = cmu_books['Plot'].apply(remove_punctuations).apply(lambda x: ' '.join([item for item in x.split() if item not in stop]))
cmu_books.head()


# In[8]:


# Joining the title and author and tokenizing the plot by word
cmu_books['Author'] = cmu_books['Author'].map(lambda x: x.split(' '))
for index, row in cmu_books.iterrows():
    cmu_books['Author'][index] = '_'.join(row['Author']).lower()
cmu_books['Plot'] = cmu_books['Plot'].str.split(" ")
cmu_books.set_index('Title', inplace = True)
cmu_books.head()


# In[9]:


# Combining the author and plot details into bag of words 
cmu_books['BOW'] = ''
columns = cmu_books.columns
for index, row in cmu_books.iterrows():
    words = ''
    for col in columns:
        if col == 'Author':
            words = words + row[col]+ ' '
        elif col == 'Author (Raw)':
            continue
        else:
            words = words + ' '.join(row[col][:100])+ ' ' # the first 100 words should be sufficiently rich in details
    row['BOW'] = words
cmu_books = cmu_books[['Author (Raw)', 'BOW']]
cmu_books.rename(columns={'Author (Raw)':'Author'}, inplace=True)
cmu_books.head()


# In[10]:


# Saving the dataframe as a csv file and as a pickle file
cmu_books.to_csv('book_summaries.csv', encoding='utf-8', sep=';', index=None)

with open('book_summaries.pkl', 'wb') as f:
    pickle.dump(cmu_books, f)

