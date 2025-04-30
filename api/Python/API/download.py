import os
import yt_dlp
from urllib.parse import urlparse, parse_qs

DOWNLOAD_FOLDER = "C:\\xampp\\htdocs\\Muse\\Data\\Mp3\\Test_mp3"
os.makedirs(DOWNLOAD_FOLDER, exist_ok=True)

def extract_video_id(youtube_url: str) -> str:
    """從 YouTube 連結中擷取影片 ID"""
    parsed_url = urlparse(youtube_url)
    query_params = parse_qs(parsed_url.query)
    return query_params.get("v", [""])[0]

def download_mp3(youtube_url: str) -> str:
    """下載 YouTube 音訊為 MP3"""
    video_id = extract_video_id(youtube_url)
    output_filename = os.path.join(DOWNLOAD_FOLDER, f"{video_id}.mp3")

    ydl_opts = {
        'format': 'bestaudio',
        'extract_audio': True,
        'audio_format': 'mp3',
        'outtmpl': output_filename,
        'postprocessors': []
    }

    with yt_dlp.YoutubeDL(ydl_opts) as ydl:
        ydl.download([youtube_url])

    return DOWNLOAD_FOLDER
