import pandas as pd
from sklearn.tree import DecisionTreeClassifier
import joblib

# Load dataset
data = pd.read_csv('symptoms_dataset.csv')

# Features and target
X = data.drop('Disease', axis=1)
y = data['Disease']

# Train Decision Tree
model = DecisionTreeClassifier()
model.fit(X, y)

# Save model
joblib.dump(model, 'model/symptom_checker_model.joblib')

print("Model trained and saved successfully!")


