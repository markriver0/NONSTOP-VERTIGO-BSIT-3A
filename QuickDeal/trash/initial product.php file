<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product</title>
    <link rel="stylesheet" href="product.css" />
  </head>

  <body>
    <?php include 'navbar.php'; ?>

    <button id="dark-mode-toggle" class="dark-mode-toggle">
      <span>🌙</span>
    </button>

    <main>
      <section class="product-detail">
        <div class="container">
          <div class="product-main">
            <div class="gallery">
              <img
                class="main-image"
                src="https://raketcontent.com/01_min_118f63ec03.jpg"
                alt="Product"
              />
              <div class="thumbnails">
                <img
                  src="https://cdn.dribbble.com/userupload/43067731/file/original-bc14b76f0cbc00effd0e9e69b24d01d8.png?resize=1024x768&vertical=center"
                  alt=""
                />
                <img
                  src="https://i.ytimg.com/vi/_er2XDvyQ28/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLCHhBEbS8fTlQhTEk-0-mUmkviteg"
                  alt=""
                />
                <img
                  src="https://raketcontent.com/01_min_118f63ec03.jpg"
                  alt=""
                />
                <img
                  src="https://raketcontent.com/01_min_118f63ec03.jpg"
                  alt=""
                />
                <img
                  src="https://raketcontent.com/01_min_118f63ec03.jpg"
                  alt=""
                />
                <img
                  src="https://raketcontent.com/01_min_118f63ec03.jpg"
                  alt=""
                />
              </div>
            </div>

            <div class="details">
              <div class="product-header">
                <div class="details-box">
                  <h1 class="title">Brand New iPhone 14</h1>
                  <p class="price">₱45,000</p>
                  <p class="category">Fashion</p>
                  <p class="condition">New</p>
                  <p>Listed 2 days ago</p>

                  <div class="message-button">
                    <button class="message">Message Seller</button>
                  </div>
                </div>
              </div>

              <div class="details-box">
                <div class="product-info">
                  <h2>More Details</h2>
                  <p class="Brand">Brand</p>
                  <p class="location">Quezon City</p>
                </div>
              </div>

              <div class="seller-information">
                <div class="seller-box">
                  <img src="" alt="" />
                  <p>Jeremy Rellama</p>
                </div>
                <p>Seller Details</p>
              </div>

              <div class="send-seller-message">
                <div class="msg-header">
                  <img
                    src="https://upload.wikimedia.org/wikipedia/commons/8/83/Facebook_Messenger_4_Logo.svg"
                    alt="Messenger"
                    class="msg-icon"
                  />
                  <span>Send seller a message</span>
                </div>
                <textarea placeholder="Hi, is this available?"></textarea>
                <button class="send">Send</button>
              </div>

              <!-- <div class="actions">
          <button class="buy">Buy Now</button>
        </div> -->
            </div>
          </div>
        </div>
      </section>
    </main>

    <div class="message-panel-overlay" id="messagePanel">
      <div class="message-panel">
        <div class="header">
          <h1>Message John Doe</h1>
          <button class="close-btn" onclick="closeMessagePanel()">×</button>
        </div>
        <div class="product-summary">
          <img
            src="https://raketcontent.com/01_min_118f63ec03.jpg"
            alt="Product"
          />
          <div>
            <p class="product-title">Brand New iPhone 14</p>
            <p class="product-price">₱45,000</p>
          </div>
        </div>
        <textarea
          placeholder="Please type your message to the seller"
        ></textarea>
        <p class="disclaimer">
          Don't share your email, phone number or financial information.
        </p>
        <div class="panel-actions">
          <button class="cancel" onclick="closeMessagePanel()">Cancel</button>
          <button class="send-message">Send Message</button>
        </div>
      </div>
    </div>

    <script>
      const messageBtn = document.querySelector(".message-button button");
      const messagePanel = document.getElementById("messagePanel");

      messageBtn.addEventListener("click", () => {
        messagePanel.style.display = "flex";
      });

      function closeMessagePanel() {
        messagePanel.style.display = "none";
      }
    </script>

    <footer><?php include 'footer.php'; ?></footer>

    <script src="bubble.js"></script>
    <script src="dark-mode.js"></script>

    <script>
      const thumbs = document.querySelectorAll(".thumbnails img");
      const mainImg = document.querySelector(".main-image");

      thumbs.forEach((thumb) => {
        thumb.addEventListener("click", () => {
          mainImg.src = thumb.src;
        });
      });
    </script>
  </body>
</html>
