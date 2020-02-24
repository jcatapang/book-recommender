#!/usr/bin/env python
# coding: utf-8

# In[1]:


import numpy as np
import pandas as pd
import pickle
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.decomposition import LatentDirichletAllocation
from sklearn.manifold import TSNE
from recommend_model import get_damlev
import heapq


# In[2]:


def load_books():
    try:
        with open('book_summaries.pkl', 'rb') as f:
            cmu_books = pickle.load(f)
        print("book_summaries.pkl has been loaded successfully")
    except:
        print("book_summaries.pkl doesn't exist")
        exit()
    return cmu_books.drop_duplicates(subset='BOW', keep="last")


# In[3]:


def load_cv_bow():
    try:
        with open('cv_bow.pkl', 'rb') as f:
            cv_bow = pickle.load(f)
        print("cv_bow.pkl has been loaded successfully")
    except:
        print("cv_bow.pkl doesn't exist")
        exit()
    return cv_bow


# In[4]:


# Using latent dirichlet allocation for topic modelling which can be used as themes or groupings
'''
groups = 10
lda_model = LatentDirichletAllocation(n_components=10,
                                      learning_method='online',
                                      max_iter=20,
                                      random_state=42)

X_topics = lda_model.fit_transform(cv_bow)
'''

def load_lda():
    try:
        with open('lda_model.pkl', 'rb') as f:
            lda_model = pickle.load(f)
        print("lda_model.pkl has been loaded successfully")
    except:
        print("lda_model.pkl doesn't exist")
        exit()
    return lda_model
    
def load_x_topics():
    try:
        with open('x_topics.pkl', 'rb') as f:
            X_topics = pickle.load(f)
        print("x_topics.pkl has been loaded successfully")
    except:
        print("x_topics.pkl doesn't exist")
        exit()
    return X_topics


# In[5]:


def save_lda(lda_model):
    with open('lda_model.pkl', 'wb') as f:
        pickle.dump(lda_model, f)

    with open('x_topics.pkl', 'wb') as f:
        pickle.dump(X_topics, f)


# In[6]:


def load_cv():
    try:
        with open('count_vectorizer.pkl', 'rb') as f:
            cv = pickle.load(f)
        print("count_vectorizer.pkl has been loaded successfully")
    except:
        print("count_vectorizer.pkl doesn't exist")
        exit()
    return cv


# In[7]:


'''
top_n = 10
topic_summaries = list()

topic_word = lda_model.components_  # Getting the topic words
vocab = cv.get_feature_names()

# Getting the ten themes in all the book summaries
for i, topic_dist in enumerate(topic_word):
    topic_words = np.array(vocab)[np.argsort(topic_dist)][:-(top_n+1):-1]
    topic_summaries.append(' '.join(topic_words))
    print('Topic {}: {}'.format(i, ' | '.join(topic_words)))
'''


# In[8]:


def map_themes(theme_list):
    theme_dict = {0:'Medieval Fantasy', 1:'Novel', 2:'Family Life', 3:'Royalty',
                  4:'Family Fantasy', 5:'Human Relationships', 6:'General Fiction', 7:'Science Fiction', 8:'Country Affairs', 9:'Speculative Fiction'}
    themes = list()
    for theme_number in theme_list:
        themes.append(theme_dict[theme_number])
    return themes


# In[9]:


def cmu_books_themed():
    unnormalized = np.matrix(X_topics)
    theme = unnormalized/unnormalized.sum(axis=1)

    #cmu_books.dropna(inplace=True)
    lda_keys = list()
    print(cmu_books['BOW'].shape[0])
    for i, plot in enumerate(cmu_books['BOW']):
        try:
            first_theme = theme[i].argmax()
            second_theme = np.delete(theme[i], theme[i].argmax()).argmax()
            if first_theme != second_theme:
                lda_keys.append([first_theme, second_theme])
            else:
                lda_keys.append([first_theme])
        except:
            continue
    
    # Assigning the topics found by LDA to each book in the dataset
    cmu_books['theme_number'] = lda_keys
    
    # Labeling the themes
    cmu_books['theme'] = cmu_books['theme_number'].apply(map_themes)
    return cmu_books


# In[10]:


def save_books_themed(cmu_books):
    # Saving the dataframe as a csv file and as a pickle file
    cmu_books.to_csv('book_themed_summaries.csv', encoding='utf-8', sep=';', index=None)

    with open('cmu_themed_books.pkl', 'wb') as f:
        pickle.dump(cmu_books, f)


# In[11]:


def load_books_themed():
    try:
        with open('cmu_themed_books.pkl', 'rb') as f:
            cmu_books = pickle.load(f)
        print("cmu_themed_books.pkl has been loaded successfully")
    except:
        print("cmu_themed_books.pkl doesn't exist")
        exit()
    return cmu_books


# In[12]:


def get_grouping(title):
    themed_df = load_books_themed()
    try:
        theme = themed_df['theme'][title].iloc[0]
    except:
        theme = themed_df['theme'][title]
    return theme

