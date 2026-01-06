import unicodedata

class TextNormalizer:
    def normalize(self, text):
        # Remove accents and lowercase
        text = text.lower()
        text = unicodedata.normalize('NFKD', text).encode('ASCII', 'ignore').decode('utf-8')
        return text.strip()
