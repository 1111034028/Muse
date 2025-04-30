from fastapi import FastAPI, HTTPException
import base64
from download import download_mp3
from audioToSpec import process_audio
from pydantic import BaseModel
app = FastAPI()

class PredictRequest(BaseModel):
    base64_link: str

@app.post("/predict")
async def predict(request: PredictRequest):
    """API：接收 Base64 YouTube 連結，回傳音樂類別"""
    try:
        youtube_url = base64.b64decode(request.base64_link).decode()
        mp3Router = download_mp3(youtube_url)
        
        predicted_genre = process_audio()
        return { "music_genre": predicted_genre,
                 "music_routur" : mp3Router }

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"處理失敗: {str(e)}")
    
@app.get("/")
async def Get():
    return {"message": "API 連線成功！"}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=5000)
