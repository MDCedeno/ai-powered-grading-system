from flask import Flask, request, jsonify
import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_squared_error
import joblib
import os

app = Flask(__name__)

# Load or train model
MODEL_PATH = 'models/grading_model.pkl'
DATA_PATH = 'data/grading_data.csv'

def load_or_train_model():
    if os.path.exists(MODEL_PATH):
        model = joblib.load(MODEL_PATH)
        print("Model loaded from file.")
    else:
        # Load training data
        if os.path.exists(DATA_PATH):
            data = pd.read_csv(DATA_PATH)
            X = data[['midterm_quizzes', 'midterm_exam', 'final_quizzes', 'final_exam']]
            y = data['final_gpa']

            X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

            model = RandomForestRegressor(n_estimators=100, random_state=42)
            model.fit(X_train, y_train)

            # Evaluate
            predictions = model.predict(X_test)
            mse = mean_squared_error(y_test, predictions)
            print(f"Model trained. MSE: {mse}")

            # Save model
            joblib.dump(model, MODEL_PATH)
        else:
            print("No training data found. Using dummy model.")
            model = RandomForestRegressor(n_estimators=100, random_state=42)
            # Dummy training
            X_dummy = np.random.rand(100, 4)
            y_dummy = np.random.rand(100)
            model.fit(X_dummy, y_dummy)

    return model

model = load_or_train_model()

@app.route('/predict_gpa', methods=['POST'])
def predict_gpa():
    data = request.json
    midterm_quizzes = data['midterm_quizzes']
    midterm_exam = data['midterm_exam']
    final_quizzes = data['final_quizzes']
    final_exam = data['final_exam']

    features = np.array([[midterm_quizzes, midterm_exam, final_quizzes, final_exam]])
    predicted_gpa = model.predict(features)[0]

    return jsonify({'predicted_gpa': round(predicted_gpa, 2)})

@app.route('/generate_quiz', methods=['POST'])
def generate_quiz():
    data = request.json
    subject = data['subject']
    difficulty = data['difficulty']

    # Dummy quiz generation
    questions = [
        {
            'question': f"What is the capital of {subject}?",
            'options': ['Option A', 'Option B', 'Option C', 'Option D'],
            'correct': 'Option A'
        },
        {
            'question': f"Explain a key concept in {subject}.",
            'type': 'essay'
        }
    ]

    return jsonify({'quiz': questions})

if __name__ == '__main__':
    app.run(debug=True, port=5000)
