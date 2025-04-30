from audio_to_spec import audio_to_spec
from prepare_data import prepare_data
from train_model import train_model
from test_model import test_model
from audio_to_spec import single_audio_to_spec as SAC

def main():
    """
    主程序入口，執行頻譜圖生成、模型訓練及測試新頻譜圖。
    """
    # start()

    output_folder = "C:\\Myproj\\Music\\Mp3\\one_test"   # 替換為頻譜圖保存資料夾路徑
    #Step 4: 使用訓練好的模型測試未讀取過的頻譜圖
    print("Step 4: 測試模型...")
    model_path = "C:\\Myproj\\Music\\Python\\music_genre_cnn_model.keras"    
    test_model(output_folder, model_path)

    print("模型測試完成！")

if __name__ == "__main__":
    main()


def start():
    # Step 1: 呼叫 audio_to_spec，將音樂轉換為頻譜圖
    print("Step 1: 生成頻譜圖...")
    input_folder = "C:\\Myproj\\Music\\Mp3\\Data\\genres_original"  # 替換為音樂檔案資料夾路徑
    output_folder = "C:\\Myproj\\Music\\Mp3\\Test"  # 替換為頻譜圖保存資料夾路徑
    audio_to_spec(input_folder, output_folder)
    print("頻譜圖生成完成！")

    print("Step 2: 測試用音檔轉頻譜圖...")
    input_folder = "C:\\Myproj\\Music\\Mp3\\Test_mp3"   # 替換為音樂檔案資料夾路徑
    output_folder = "C:\\Myproj\\Music\\Mp3\\one_test"   # 替換為頻譜圖保存資料夾路徑
    SAC(input_folder, output_folder, None)
    print("頻譜圖生成完成！")

    print("Step 3: 開始訓練模型...")
    train_model()
    print("模型訓練完成！")