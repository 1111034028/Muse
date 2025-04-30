from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Conv2D, MaxPooling2D, Flatten, Dense, Dropout
from tensorflow import keras
import json
import numpy as np
from prepare_data import prepare_data  # 導入資料設置模組
MODEL_PATH = "C:\\xampp\\htdocs\\Muse\\api\\Python\\NCC_Model\\music_genre_cnn_model.keras"

def train_model():
    """
    加載數據，訓練模型並保存
    """
    # 加載數據
    train_data, val_data, class_names = prepare_data("./mp3/test")  # 替換為您的資料夾名稱
    # 初始化模型
    # 定義模型結構
    model = Sequential([
        # input_shape => 圖片大小 (128x128) 和 RGB 通道
        Conv2D(32, (3, 3), activation='relu', input_shape=(128, 128, 3)),
        MaxPooling2D((2, 2)),
        Dropout(0.2),

        Conv2D(64, (3, 3), activation='relu'),
        MaxPooling2D((2, 2)),
        Dropout(0.3),

        Flatten(),
        Dense(128, activation='relu'),
        Dropout(0.5),
        Dense(len(class_names), activation='softmax')  # 輸出為類別數量
    ])

    # 編譯模型
    model.compile(optimizer='adam', loss='sparse_categorical_crossentropy', metrics=['accuracy'])
    
    # 訓練模型
    model.fit(
        train_data, 
        epochs = 20, 
        validation_data = val_data
    )

    # 保存模型
    model.save(MODEL_PATH)

    with open("C:\\xampp\\htdocs\\Muse\\api\\Python\\NCC_Model\\class_names.json", 'w') as f:
        json.dump(class_names, f)
    
    weights = model.get_weights()
    # 判斷權重是否為零
    if np.all([np.sum(w) == 0 for w in weights]):
        print("模型權重未更新，尚未訓練過")
    else:
        print("模型已保存！")

    return MODEL_PATH

if __name__ == "__main__":
    train_model()
