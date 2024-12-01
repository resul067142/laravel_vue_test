import sys
import json
import cv2
import base64
import os

def analyze_image(image_path):
    """
    Görüntü dosyasını analiz eder ve tespit edilen yüzlerin base64 formatında görüntülerini döndürür.
    """
    print(f"[INFO] Görüntü dosyası işleniyor: {image_path}", file=sys.stderr)

    # Görüntüyü yükle
    img = cv2.imread(image_path)
    if img is None:
        print(f"[ERROR] Geçersiz veya okunamayan görüntü: {image_path}", file=sys.stderr)
        return []

    # Yüz tanıma için Haar Cascade sınıflandırıcısını yükle
    haar_cascade_path = cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'
    if not os.path.exists(haar_cascade_path):
        print("[ERROR] Haar Cascade dosyası bulunamadı!", file=sys.stderr)
        return []

    face_cascade = cv2.CascadeClassifier(haar_cascade_path)

    # Görüntüyü gri tonlamaya çevir
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # Yüzleri tespit et
    faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=4)
    print(f"[INFO] {len(faces)} yüz tespit edildi.", file=sys.stderr)

    face_images = []
    for (x, y, w, h) in faces:
        # Yüz bölgesini kırp
        face_img = img[y:y + h, x:x + w]
        # Görüntüyü base64 formatına çevir
        _, buffer = cv2.imencode('.jpg', face_img)
        face_base64 = base64.b64encode(buffer).decode('utf-8')
        face_images.append({
            'image': 'data:image/jpeg;base64,' + face_base64
        })

    return face_images


def analyze_video(video_path):
    """
    Video dosyasını analiz eder ve tespit edilen yüzlerin base64 formatında görüntülerini döndürür.
    """
    print(f"[INFO] Video dosyası işleniyor: {video_path}", file=sys.stderr)

    cap = cv2.VideoCapture(video_path)
    if not cap.isOpened():
        print(f"[ERROR] Video dosyası açılamadı: {video_path}", file=sys.stderr)
        return []

    # Yüz tanıma için Haar Cascade sınıflandırıcısını yükle
    haar_cascade_path = cv2.data.haarcascades + 'haarcascade_frontalface_default.xml'
    if not os.path.exists(haar_cascade_path):
        print("[ERROR] Haar Cascade dosyası bulunamadı!", file=sys.stderr)
        return []

    face_cascade = cv2.CascadeClassifier(haar_cascade_path)
    face_images = []
    frame_count = 0

    while True:
        ret, frame = cap.read()
        if not ret:
            break

        # Her 10 karede bir işlem yapalım (performans için)
        if frame_count % 10 == 0:
            gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
            faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=4)
            print(f"[INFO] Kare {frame_count}: {len(faces)} yüz tespit edildi.", file=sys.stderr)

            for (x, y, w, h) in faces:
                face_img = frame[y:y + h, x:x + w]
                _, buffer = cv2.imencode('.jpg', face_img)
                face_base64 = base64.b64encode(buffer).decode('utf-8')
                face_images.append({
                    'image': 'data:image/jpeg;base64,' + face_base64
                })

        frame_count += 1

    cap.release()
    return face_images


def main():
    """
    Komut satırı argümanlarından dosya yollarını alır, her dosya için uygun analiz fonksiyonunu çağırır
    ve sonuçları JSON formatında yazdırır.
    """
    # Komut satırı argümanlarından dosya yollarını al
    if len(sys.argv) < 2:
        print("[ERROR] Lütfen bir veya daha fazla dosya yolu belirtin.", file=sys.stderr)
        sys.exit(1)

    try:
        file_paths = json.loads(sys.argv[1])
        print("[INFO] Alınan dosya yolları:", file_paths, file=sys.stderr)
    except json.JSONDecodeError:
        print("[ERROR] Geçersiz JSON formatı.", file=sys.stderr)
        sys.exit(1)

    all_faces = []

    for path in file_paths:
        if not os.path.exists(path):
            print(f"[ERROR] Dosya bulunamadı: {path}", file=sys.stderr)
            continue

        print(f"[INFO] İşlenen dosya: {path}", file=sys.stderr)
        extension = os.path.splitext(path)[1].lower()
        if extension in ['.jpg', '.jpeg', '.png']:
            faces = analyze_image(path)
        elif extension in ['.mp4', '.avi', '.mov', '.mkv']:
            faces = analyze_video(path)
        else:
            print(f"[ERROR] Desteklenmeyen dosya türü: {extension}", file=sys.stderr)
            faces = []

        all_faces.extend(faces)

    # Sonuçları JSON formatında yazdır
    print(json.dumps(all_faces, indent=2))

if __name__ == "__main__":
    main()
