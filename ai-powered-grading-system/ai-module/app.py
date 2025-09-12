from flask import Flask, request, jsonify
import random

app = Flask(__name__)

@app.route('/grade', methods=['POST'])
def grade_essay():
    data = request.json
    essay = data.get('essay', '')
    if not essay:
        return jsonify({'error': 'No essay provided'}), 400

    # Mock AI grading
    grade = random.randint(70, 100)
    feedback = f"This is a mock feedback for the essay. Grade: {grade}/100. Strengths: Good structure. Areas for improvement: More details needed."

    return jsonify({
        'grade': grade,
        'feedback': feedback,
        'ai_used': 'Mock AI'
    })

@app.route('/quiz', methods=['POST'])
def generate_quiz():
    data = request.json
    topic = data.get('topic', 'General')
    num_questions = data.get('num_questions', 5)

    # Mock quiz generation
    questions = []
    for i in range(num_questions):
        questions.append({
            'question': f"What is the capital of France? (Mock question {i+1} on {topic})",
            'options': ['Paris', 'London', 'Berlin', 'Madrid'],
            'answer': 'Paris'
        })

    return jsonify({
        'topic': topic,
        'questions': questions,
        'ai_used': 'Mock AI'
    })

@app.route('/analytics', methods=['POST'])
def analyze_grades():
    data = request.json
    grades = data.get('grades', [])
    if not grades:
        return jsonify({'error': 'No grades provided'}), 400

    # Mock analytics
    avg_grade = sum(grades) / len(grades)
    insights = f"Average grade: {avg_grade:.2f}. Distribution: Most students scored between 80-90. Recommendations: Focus on weak areas."

    return jsonify({
        'average': avg_grade,
        'insights': insights,
        'ai_used': 'Mock AI'
    })

@app.route('/compute_grade', methods=['POST'])
def compute_grade():
    data = request.json
    midterm_quizzes = data.get('midterm_quizzes', 0)
    midterm_exam = data.get('midterm_exam', 0)
    final_quizzes = data.get('final_quizzes', 0)
    final_exam = data.get('final_exam', 0)

    # Mock AI computation
    midterm_grade = (midterm_quizzes + midterm_exam) / 2
    final_grade = (final_quizzes + final_exam) / 2
    overall = (midterm_grade + final_grade) / 2
    gpa = min(4.0, overall / 25)  # Assuming 100 scale to 4.0 GPA

    return jsonify({
        'midterm_grade': midterm_grade,
        'final_grade': final_grade,
        'overall': overall,
        'gpa': gpa,
        'ai_used': 'Mock AI'
    })

if __name__ == '__main__':
    app.run(debug=True, port=5000)
