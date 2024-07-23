from flask import Flask, request, jsonify
import numpy as np
import tensorflow as tf
from sklearn.preprocessing import LabelEncoder, MinMaxScaler
import os
import joblib

app = Flask(__name__)

# Memuat model LSTM yang telah dilatih
model_path = './models/lstm_model.keras'
model = tf.keras.models.load_model(model_path)

# Memuat scaler yang telah dilatih
scaler_y_path = './scalers/price_scaler.pkl'
scaler_y = joblib.load(scaler_y_path)

scaler_X_path = './scalers/feature_scaler.pkl'
scaler_X = joblib.load(scaler_X_path)

# Inisialisasi LabelEncoder untuk setiap kolom kategorikal
encoders = {}
categorical_columns = ['VGA', 'Processor', 'Penyimpanan']   

# Fungsi untuk memuat encoder yang telah dilatih
def load_encoders():
    for col in categorical_columns:
        encoder = LabelEncoder()
        encoder.classes_ = np.load(f'./encoders/{col}_encoder.npy', allow_pickle=True)
        encoders[col] = encoder

# Memuat semua encoder
load_encoders()

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json(force=True)
    
    # Menyiapkan input untuk prediksi
    try:
        encoded_data = {}
        for col in categorical_columns:
            value = data.get(col, None)
            if value is not None:
                encoder = encoders.get(col, None)
                if encoder is not None:
                    if value not in encoder.classes_:
                        return jsonify({'error': f'Value "{value}" for column "{col}" is not recognized by the encoder'}), 400
                    encoded_data[col] = encoder.transform([value])[0]
                else:
                    return jsonify({'error': f'Encoder for {col} not found'}), 500

        # Menyiapkan data untuk model
        input_data = np.array([[encoded_data[col] for col in categorical_columns] + [data['Ukuran Layar'], data['RAM']]])
        
        # Scale the input data
        input_data_scaled = scaler_X.transform(input_data)
        
        # Reshape the data for LSTM
        input_data_reshaped = input_data_scaled.reshape((1, input_data_scaled.shape[1], 1))
        
        # Melakukan prediksi
        prediction = model.predict(input_data_reshaped)
        harga_scaled = float(prediction[0][0])
        
        # Inverse transform to get actual price
        harga = scaler_y.inverse_transform([[harga_scaled]])[0][0]
        
        return jsonify({'harga': harga})

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
