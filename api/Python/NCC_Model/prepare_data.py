import tensorflow as tf

def prepare_data(folder_path):
    """
    加載並準備訓練和驗證數據
    :param folder_path: 圖片資料主資料夾的路徑
    :return: 訓練數據集、驗證數據集以及類別名稱列表
    """
    # 從資料夾中加載數據集
    train_data = tf.keras.utils.image_dataset_from_directory(
        folder_path,
        image_size=(128, 128),  # 圖片大小調整為 128x128
        batch_size=32,
        subset='training',
        validation_split=0.2,  # 劃分 20% 為驗證集
        seed=123
    )

    val_data = tf.keras.utils.image_dataset_from_directory(
        folder_path,
        image_size=(128, 128),
        batch_size=32,
        subset='validation',
        validation_split=0.2,
        seed=123
    )

    # 提取類別名稱
    class_names = train_data.class_names

    # 正規化數據
    def normalize_images(image, label):
        image = tf.cast(image, tf.float32) / 255.0
        return image, label

    train_data = train_data.map(normalize_images)
    val_data = val_data.map(normalize_images)

    print(f"類別名稱：{class_names}")
    return train_data, val_data, class_names

if __name__ == "__main__":
    prepare_data("./mp3/test")