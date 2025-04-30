import requests
import base64

# 配置 Client ID 和 Client Secret
CLIENT_ID = "e3441c6a5c2e4d96948efe428eedc915"
CLIENT_SECRET = "599d9149c7f040c59c595f529c529e5b"

# 獲取 OAuth Token
def get_token():
    url = "https://accounts.spotify.com/api/token"
    headers = {
        "Authorization": "Basic " + base64.b64encode(f"{CLIENT_ID}:{CLIENT_SECRET}".encode()).decode()
    }
    data = {"grant_type": "client_credentials"}
    response = requests.post(url, headers=headers, data=data)
    return response.json()["access_token"]

# 搜索歌曲並檢查是否有成人標籤，並提取演唱者名稱
def search_and_check_explicit(query):
    token = get_token()
    search_url = f"https://api.spotify.com/v1/search?q={query}&type=track&limit=10"
    headers = {"Authorization": f"Bearer {token}"}
    response = requests.get(search_url, headers=headers)
    search_data = response.json()

    if "tracks" in search_data and "items" in search_data["tracks"]:
        for track in search_data["tracks"]["items"]:
            track_name = track["name"]
            track_id = track["id"]
            explicit = track["explicit"]
            # 提取演唱者名稱
            artists = [artist["name"] for artist in track["artists"]]
            artist_names = ", ".join(artists)
            print(f"歌曲名稱: {track_name}, ID: {track_id}, 成人標籤: {'是' if explicit else '否'}, 演唱者: {artist_names}")
    else:
        print("未找到相關歌曲。")

# 測試函數
search_query = ""  # 替換為你想查詢的歌曲名稱
search_and_check_explicit(search_query)
