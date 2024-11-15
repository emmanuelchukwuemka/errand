from flask import Flask, render_template, request, redirect, flash, url_for
from flask_sqlalchemy import SQLAlchemy

# Initialize Flask application
app = Flask(__name__)
app.config['SECRET_KEY'] = 'your_secret_key'  # Replace with a secure secret key
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///market.db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

# Initialize the database
db = SQLAlchemy(app)

# Define Item model for database
class Item(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    description = db.Column(db.String(500), nullable=False)

# Route to the homepage 
@app.route('/', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        description = request.form.get('item_description')
        
        # Add new item to database
        if description:
            new_item = Item(description=description)
            db.session.add(new_item)
            db.session.commit()
            flash("Item added to cart!", "success")
            return redirect(url_for('index'))
        else:
            flash("Please provide an item description.", "warning")
            
    return render_template('index.html')

# Initialize the database tables
with app.app_context():
    db.create_all()

if __name__ == '__main__':
    app.run(debug=True)
