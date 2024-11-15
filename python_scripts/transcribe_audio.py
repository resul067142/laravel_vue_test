import whisper

def transcribe_audio(file_path):
    model = whisper.load_model("base")
    result = model.transcribe(file_path)
    return result["text"]

if __name__ == "__main__":
    import sys
    if len(sys.argv) < 2:
        print("Lütfen bir ses dosyası yolu belirtin.")
    else:
        audio_file_path = sys.argv[1]
        transcript = transcribe_audio(audio_file_path)
        print(transcript)
