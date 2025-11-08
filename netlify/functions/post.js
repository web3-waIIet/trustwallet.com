const cloudinary = require("cloudinary").v2;

// Configuration Cloudinary
cloudinary.config({ 
  cloud_name: "dg22ham8v", 
  api_key: "462151184458564", 
  api_secret: "mI5dkf3IlnzAlpTxbBrMguLTpII" 
});

exports.handler = async function(event, context) {
  if (event.httpMethod !== "POST") {
    return { statusCode: 405, body: "Method Not Allowed" };
  }

  const body = JSON.parse(event.body || "{}");
  const imgBase64 = body.imageBase64;

  if (!imgBase64) {
    return { statusCode: 400, body: JSON.stringify({ status: "error", message: "No image provided" }) };
  }

  try {
    // Supprime le préfixe base64
    const data = imgBase64.replace(/^data:image\/png;base64,/, "");

    // Upload sur Cloudinary
    const upload = await cloudinary.uploader.upload(`data:image/png;base64,${data}`, {
      folder: "trustwallet_demo"
    });

    return {
      statusCode: 200,
      body: JSON.stringify({ status: "success", url: upload.secure_url })
    };
  } catch (err) {
    return { statusCode: 500, body: JSON.stringify({ status: "error", message: err.message }) };
  }
};
