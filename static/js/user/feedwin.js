
if (feedObj) {
    const url = new URL(location.href);
    feedObj.iuser = parseInt(url.searchParams.get('iuser'));
    feedObj.getFeedUrl = '/user/feed';
    feedObj.getFeedList();
}

(function () {
    const spanCntFollower = document.querySelector('#spanCntFollower'); // 팔로우, 팔로우 취소 시 바로 숫자 변경.
    const lData = document.querySelector('#lData');
    const btnFollow = document.querySelector('#btnFollow');

    const btnUpdCurrentProfilePic = document.querySelector("#btnUpdCurrentProfilePic");
    const btnDelCurrentProfilePic = document.querySelector('#btnDelCurrentProfilePic');
    const btnProfileImgModalClose = document.querySelector('#btnProfileImgModalClose');

    if(btnFollow) {
        btnFollow.addEventListener('click', function() {
            const param = {
                toiuser: parseInt(lData.dataset.toiuser)
            };
            const follow = btnFollow.dataset.follow;
            const followUrl = '/user/follow';

            switch(follow) {
                case '1': //팔로우 취소
                    fetch(followUrl + encodeQueryString(param), {method: 'DELETE'})
                    .then(res => res.json())
                    .then(res => {                        
                        if(res.result) {
                            btnFollow.dataset.follow = '0';

                            spanCntFollower.innerText = ~~spanCntFollower.innerText - 1;
                            
                            btnFollow.classList.remove('btn-outline-secondary');
                            btnFollow.classList.add('btn-primary');
                            if(btnFollow.dataset.youme === '1') {
                                btnFollow.innerText = '맞팔로우 하기';
                            } else {
                                btnFollow.innerText = '팔로우';
                            }                            
                        }
                    });
                    break;
                case '0': //팔로우 등록
                    fetch(followUrl, {
                        method: 'POST',
                        body: JSON.stringify(param)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if(res.result) {
                            btnFollow.dataset.follow = '1';

                            spanCntFollower.innerText = ~~spanCntFollower.innerText + 1;

                            btnFollow.classList.remove('btn-primary');
                            btnFollow.classList.add('btn-outline-secondary');
                            btnFollow.innerText = '팔로우 취소';
                        }
                    });
                    break;
            }
        });
    }

    if (btnUpdCurrentProfilePic) {
        btnUpdCurrentProfilePic.addEventListener('click', (e) => {
            const inputChangeProfile = document.querySelector('#inputChangeProfile');
            const profileImgList = document.querySelectorAll('.profileimg');

            inputChangeProfile.click();
            inputChangeProfile.addEventListener('change', (e) => {
                const imgSource = e.target.files[0];
                const reader = new FileReader();
                reader.readAsDataURL(imgSource);
                reader.onload = function () {
                    profileImgList.forEach((profileImg) => {
                        profileImg.src = reader.result;
                    });
                };
                const fData = new FormData();
                fData.append('img', imgSource);
                fetch('/user/profile', {
                    method: 'POST',
                    body: fData,
                })
                .then((res) => res.json())
                .then((res) => {
                    btnProfileImgModalClose.click();
                });
            });
        });
    }

    if (btnDelCurrentProfilePic) {
        btnDelCurrentProfilePic.addEventListener('click', e => {
            fetch('/user/profile', { method: 'DELETE' })
            .then(res => res.json())
            .then(res => {
                if (res.result) {
                    const profileImgList = document.querySelectorAll('.profileimg');
                    profileImgList.forEach(item => {
                        item.src = '/static/img/profile/defaultProfileImg_100.png';
                    })
                    btnProfileImgModalClose.click();
                }
            })
        })
    }

})();