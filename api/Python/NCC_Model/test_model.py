import os
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.image import load_img, img_to_array
import json
import numpy as np
CLASS_NAMES_FILE = "C:\\xampp\\htdocs\\Muse\\api\\Python\\NCC_Model\\class_names.json"

def getClass(folder_path, model_path):
    """
    測試資料夾內的所有圖片並預測它們的類別
    :param folder_path: 測試圖片的資料夾路徑
    :param model_path: 訓練好的模型路徑
    :param class_names: 類別名稱列表
    """
    # 加載已保存的模型
    model = load_model(model_path)

    with open(CLASS_NAMES_FILE , 'r') as f:
        class_names = json.load(f)
    print("類別名稱:", class_names)

    # 遍歷資料夾中的每張圖片
    for file_name in os.listdir(folder_path):
        image_path = os.path.join(folder_path, file_name)
        
        if file_name.endswith(('.png', '.jpg', '.jpeg')):  # 確保文件為圖片格式
            try:
                # 加載圖片並調整大小
                img = load_img(image_path, target_size=(128, 128))
                img_array = img_to_array(img) / 255.0  # 正規化像素值
                img_array = np.expand_dims(img_array, axis=0)  # 增加批次維度

                # 使用模型進行預測
                predictions = model.predict(img_array)

                predicted_index = np.argmax(predictions)  # 獲得概率最大值的索引
                predicted_label = class_names[predicted_index]

                music_genres = []  # 創建一個空列表
                music_genres.append(predicted_label)

                print(f"圖片: {file_name}, 預測類別: {predicted_label}")

            except Exception as e:
                print(f"處理圖片 {file_name} 時發生錯誤: {e}")
                
    return music_genres
if __name__ == "__main__":
    # 測試資料夾路徑
    folder_path = "C:\\Myproj\\muse_backend_repo\\Mp3\\Test_spec"  # 替換為包含測試圖片的資料夾路徑
    model_path = "C:\\Myproj\\muse_backend_repo\\music_genre_cnn_model.keras"

    # 假設類別名稱來自訓練過程中
    # class_names = ["blues", "classical", "country", "disco", "hiphop", "jazz", "metal", "pop", "reggae", "rock"]  # 更新為真實類別名稱
    test_model(folder_path, model_path)
