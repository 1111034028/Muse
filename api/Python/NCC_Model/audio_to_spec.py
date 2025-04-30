import os
import librosa
import librosa.display
import numpy as np
import matplotlib.pyplot as plt

def audio_to_spec(input_folder, output_folder):
    """
    將指定資料夾中的所有 MP3/WAV 文件轉換為頻譜圖，並根據子資料夾名稱標記其音樂類別。
    :param input_folder: 包含音樂類別子資料夾的主資料夾路徑
    :param output_folder: 保存頻譜圖的目標資料夾路徑
    """
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)
    # 遍歷每個子資料夾（代表音樂類別）
    for label in os.listdir(input_folder):
        label_path = os.path.join(input_folder, label)

        # 為該類別創建一個對應的輸出子資料夾
        label_output_folder = os.path.join(output_folder, label)
        
        single_audio_to_spec(label_path, label_output_folder, label )

def single_audio_to_spec(label_path, output_folder, label):
    """
    將單首歌曲轉換為頻譜圖，並保存為圖片
    :param input_audio_path: 音樂檔案路徑 (MP3/WAV)
    :param output_folder: 圖片輸出檔案路徑
    :param label: 子資料夾檔名
    """
    # 如果目標資料夾不存在，則創建它
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    # 遍歷該類別資料夾中的所有 MP3/WAV 文件
    for file_name in os.listdir(label_path):
        file_path = os.path.join(label_path, file_name)

        #     single_audio_to_spec(file_path, label_output_folder, file_name, label)
        if(file_name.endswith('.mp3') or file_name.endswith('.wav')):
            try:
                # 加載音樂文件
                y, sr = librosa.load(file_path, duration = None)  # 加載音頻並限制長度為 30 秒

                # 生成梅爾頻譜圖
                mel_spec = librosa.feature.melspectrogram(y=y, sr=sr)
                mel_spec_db = librosa.power_to_db(mel_spec, ref=np.max)

                # 創建頻譜圖並保存為圖片
                plt.figure(figsize=(10, 4))
                librosa.display.specshow(mel_spec_db, sr=sr, x_axis='time', y_axis='mel')
                plt.axis('off')  # 隱藏坐標軸
                plt.tight_layout()

                output_file_name = os.path.splitext(file_name)[0] + ".png"

                print_str = f"成功轉換音樂: {output_file_name} -> "
                output_path = os.path.join(output_folder, output_file_name)

                if(label == None):
                    print_str += f"{output_path} | 類別: 無"
                else:
                    print_str += f"{output_path} | 類別: {label}"

                # 保存圖片
                plt.savefig(output_path, dpi=100, bbox_inches='tight', pad_inches=0)
                plt.close()

                print(print_str)

            except Exception as e:
                print(f"處理音樂文件時發生錯誤: {e}")
                
if __name__ == "__main__":
    # 測試函式
    input_folder = "./Mp3/Data/genres_original"  # 替換為包含子資料夾的主資料夾路徑（每個子資料夾為一個類別）
    output_folder = "./Mp3/test"  # 替換為保存頻譜圖的目標資料夾路徑
    audio_to_spec(input_folder, output_folder)