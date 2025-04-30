import os
import sys

parent_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "NCC_Model"))
sys.path.append(parent_dir)

from audio_to_spec import single_audio_to_spec as SAC
from test_model import getClass as Models_class

SPECTROGRAM_FOLDER = "C:\\xampp\\htdocs\\Muse\\Data\\Mp3\\Test_spec"
MODEL_PATH = "C:\\xampp\\htdocs\\Muse\\api\\Python\\NCC_Model\\music_genre_cnn_model.keras"
 
def process_audio():
    """處理 MP3 音檔，轉換為頻譜圖並預測音樂類型"""
    Music_foledr = "C:\\xampp\\htdocs\\Muse\\Data\\Mp3\\Test_mp3"
    SAC(Music_foledr, SPECTROGRAM_FOLDER, None)
    
    predicted_genre = Models_class(SPECTROGRAM_FOLDER, MODEL_PATH)

    return predicted_genre
