from flask import Flask, render_template, url_for, json
from flask_sqlalchemy import SQLAlchemy
from urllib import request

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = "sqlite:///database.db"
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['SQLALCHEMY_COMMIT_ON_TEARDOWN'] = True
db = SQLAlchemy()
db.init_app(app)


class Regions(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    country_code = db.Column(db.String(25))
    country_name = db.Column(db.String(100))
    total_views = db.Column(db.Integer, default=0)

def view_counter():
	client = json.loads(request.urlopen("http://ip-api.com/json").read())
	db_total_view = Regions.query.filter_by(country_code=client["countryCode"]).first()
	if db_total_view:
		Regions.query.filter_by(id=db_total_view.id).update(dict(total_views=db_total_view.total_views+1))
		db.session.commit()
	

@app.route("/")
def home():
	view_counter()
	regions_data = Regions.query.filter(Regions.total_views > 0)
	return render_template("home.html", regions_data=regions_data)



if __name__=="__main__":
	app.run(debug=True)