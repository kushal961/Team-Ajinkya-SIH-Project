import sys
import joblib
import json

# Load model
model = joblib.load('model/symptom_checker_model.joblib')

# Get input symptoms from PHP
input_data = sys.argv[1]
symptom_list = input_data.split(',')

# Predefined symptoms in order
symptoms = ['Fever', 'Cough', 'Headache', 'Sore Throat', 'Fatigue', 'Body Pain', 'Nausea']

# Convert input to binary array
input_features = [1 if symptom in symptom_list else 0 for symptom in symptoms]

# Predict disease
prediction = model.predict([input_features])[0]

# Return result to PHP
print(json.dumps({"disease": prediction}))

