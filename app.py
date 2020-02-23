import flask
from flask import request
import recommend_model as rec

app = flask.Flask(__name__)
app.config["DEBUG"] = True


@app.route('/', methods=['GET'])
def home():
	return "<h1>Book Recommender System for Yaraku</h1>"

@app.route('/recommend', methods=['GET'])
def recommend():
	title = request.args.get('title')
	number = request.args.get('number')
	return str(rec.get_n_recs(title, int(number)))

if __name__ == "__main__":
	app.run()