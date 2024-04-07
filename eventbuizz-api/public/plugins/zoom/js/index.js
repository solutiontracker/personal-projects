(function(){
    ZoomMtg.preLoadWasm();
	ZoomMtg.prepareJssdk();
	ZoomMtg.init({
		leaveUrl:  window.location.href,
		isSupportAV: !0,
		disableInvite: !0,
		success: function () {
			ZoomMtg.join(
				{
					meetingNumber: meeting_id,
					userName: userName,
					userEmail: userEmail,
					signature: signature,
					apiKey: apiKey,
					passWord: password || "",
					success: function(res){
						console.log('join meeting success');
					},
					error: function(res) {
						console.log(res);
					}
				}
			);
		},
		error: function(res) {
			console.log(res);
		}
	});
})();
